## DDD with Symfony and Doctrine

This project shows some basics using Domain events (part of Domain Driven Design) on top of
Symfony and Doctrine.
Used domain is a user's tasks (like to-do list).
Here described a solution to record events in the Task entity and then dispatch them at the moment the Task is persisted and saved to the database.

## Init project

```composer install```

## Unit Tests
In the project directory, you can run minimal set of unit tests:

```./bin/phpunit```  

## Run application
To use application, in the project directory run (requires [Symfony CLI](https://symfony.com/download)):

```symfony server:start```  

## REST examples (Task entity)

```curl -X GET 'http://127.0.0.1:8000/tasks';```  
```curl -X POST 'http://127.0.0.1:8000/tasks' -H 'content-type: application/json' --data-binary '{"title":"My new task"}';```  
```curl -X PUT 'http://127.0.0.1:8000/tasks/83d5c82d-4789-4ac5-8af3-9c6d44af6ab7' -H 'content-type: application/json' --data-binary '{"title":"Changed title"}';```  
```curl -X DELETE 'http://127.0.0.1:8000/tasks/83d5c82d-4789-4ac5-8af3-9c6d44af6ab7';```  

## Logging events and method calls

While sending requests above you can also see useful log information. For example:

```tail -f var/log/dev.log```