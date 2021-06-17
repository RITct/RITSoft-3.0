# RITSoft-3.0

A web application for RIT students and teachers for academic management. Developed using larvel.

## Prerequisite

- [Docker](https://www.docker.com/): Container Tool

## Getting Started

### First Step

Verify docker and docker-compose is installed by running
```
docker --version
docker-compose --version
```

### Second Step
Run
```
docker-compose build
docker-compose up -d
```
Visit `localhost:8000` to view the app.

**Run `docker-compose build` only if you're setting up for the first time or have made changes.**

If you're setting up for the first time, run
```
docker-compose run app sh initialsetup.sh
```

To stop the container run `docker-compose down`


## Contributors Guideline

[Contributing](Contributing.MD)

### Setting up your IDE and Code Inspection

So far the easiest way I've seen is just copying the vendor directory from the container to host. 

```
sudo docker cp ritsoft_app:/ritsoft/vendor /absolute/path/to/RITSoft-3.0/src/
```
Better alternatives are always welcome

## Licence

## Docs

## Contributors
