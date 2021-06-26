# Request System

A detailed explanation of the request system and its API, which is the backbone of Semester Registration.

Quick Note: This system is best implemented in a NoSQL DB, but due to technological limitations a we implemented a NoSQL in an SQL System with a ton of redundancies.

## Table of Contents
- [Tables](#tables)
- [Process Flow](#process-flow)
- [The API](#the-api)
    - [RequestStates Enum](#appenumsrequeststates)
    - [RequestTypes Enum](#appenumsrequesttypes)
    - [Create Request](#requestmodelcreatenewrequeststring-type-model-model-string-primaryval-array-payload-array-signees-void)
    - [Current Signee](#requestmodelcurrentsignee-requestsignee)
    - [Is Last Request Pending](#requestmodelislastrequestispendingstring-requesttype-string-primaryval-bool)
    - [Perform Updation](#requestmodelperformupdation-void)
    - [Reject](#requestmodelrejectremark-void)
    - [Set Next Signee](#requestmodelsetnextsigneeremark-bool)

### Tables

![Database Tables](./images/RequestTables.png)

### Process flow

The process flow for student uploading an image.

1. Student with admission id 19brxxxxx, uploads the image in [PhotoUploadController@create](../src/app/Http/Controllers/PhotoUploadController.php)
2. A new request is generated with the student's staff advisor as the only signee.
```
{
    "id": 1,
    "type": STUDENT_PHOTO_UPLOAD,
    "signees": [
        {
            "user_id": <staff_advisor_user_id>,
            "request_id": 1,
            "position": 1,
            "state": PENDING,
            "remark": null
        }
     ],
     "state": PENDING,
     "current_position": 1,
     "payload": {"photo_url": "<new_url>"},
     "table_name": "students",
     "primary_field": "admission_id",
     "primary_value": "19brxxxxx"
}
```
3. Now the request will be visible in the [RequestController@index](../src/app/Http/Controllers/RequestController.php) for this staff advisor.
4. If the staff advisor approves the request, their `state` would be changed to "approved".
5. Since he's the last and only signee, this request is approved, and the `state` would change to "approved" and a database query will be built from `table_name`, `primary_field`, `primary_value`, and `payload`.
Basically all the changes in the `payload` would be reflected in the student's table.

This is a double edged sword, while this method simplifies things for us, it also creates a major security hole.

The profile identified from `table_name`, `primary_field` and `primary_value`, would be updated with the `payload`. 

So it is important to ensure that all these attributes can't be edited after creating and proper validation must be performed before creating a request.

### The API

**NOTE: None of these functions have object level permission validations, so only call these functions after performing them.**

#### `\App\Enums\RequestStates`

Enum containing all possible states

#### `\App\Enums\RequestTypes`

Enum containing all possible types of requests

#### `RequestModel::createNewRequest(string $type, Model $model, string $primaryVal, array $payload, array $signees): void`

Function to create a new request and its signees.

#### `RequestModel.currentSignee(): RequestSignee`

Function to return the current signee's signee instance.

#### `RequestModel::isLastRequestIsPending(string $requestType, string $primaryVal): bool`

Function to check if the same request earlier requested by the same user is still pending.

#### `RequestModel.performUpdation(): void`

Execute the query using `payload`, `table_name`, `primary_field` and `primary_value`.

**WARNING: DONT EXECUTE THIS WITHOUT VALIDATION**

#### `RequestModel.reject($remark): void`

Function to reject a request.

#### `RequestModel.setNextSignee($remark): bool`

Function to shift to the next signee, returns true if all signees approved, false otherwise