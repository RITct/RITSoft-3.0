## A few things to know in PHP/Laravel.

Add new things as we progress.

#### Arrays and hashmaps are the same

```
// As array
$a = [1, 2, 3]

// As hashmap
$b = [
    "key" => "val"
]
```
Internally PHP uses the `[]` as a hashmap, it provides an abstraction layer to use the same as array.

#### Classes in PHP

- Object members can be accessed by `->` not `.`
```
class A{
    public function hi(){
        // stuff
    }
}

$b = A();
$b->hi();
```
- `$this` can be used inside the class to access the current object.
```
class A{
    public function hey(){
        // stuff
    }
    public function hi(){
        $this->hey()
    }
}
```
- Constructor
```
class A{
    public function __contruct(){
    }
}
```
- Access static members with `::`
```
class A{
    public static function any(){
        // static fun
    }
}

A::any()
```

#### Querying with Eloquent

- Minimize your sql queries by using `with` to fetch all necessary related data.
- Eloquent queries won't be executed until, methods like `get` or `all` is called.<br/>
  Use this to your advantage by splitting queries, for different use cases. For an example refer the `Attendance` Model.
  