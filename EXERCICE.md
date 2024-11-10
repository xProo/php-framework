# OOP PHP exercicexs <!-- omit in toc -->

## Table of content <!-- omit in toc -->
- [About this exercice](#about-this-exercice)
- [Pre requisite](#pre-requisite)
- [Installation](#installation)
- [Objectives](#objectives)
  - [What this application is about](#what-this-application-is-about)
    - [How it works](#how-it-works)
  - [How to submit your solutions](#how-to-submit-your-solutions)
- [Exercices](#exercices)
  - [Exercice 1 /5](#exercice-1-5)
  - [Exercice 2 /3](#exercice-2-3)
  - [Exercice 3 /5](#exercice-3-5)
  - [Exercice 4 /4](#exercice-4-4)
  - [Exercice 5 /3](#exercice-5-3)
    - [Bonus points](#bonus-points)
      - [uri pattern +4](#uri-pattern-4)
      - [Git repository +2](#git-repository-2)

## About this exercice


## Pre requisite

You need to have these installed on your machine :

- docker engine
- docker compose

You would also need to know how PHP works. You need to know OOP to get the most out of the exercices.

## Installation

In order to install the project, click on this button on the page to fork it :

![fork button](assets/fork.png)

Github will clone this repository on your account so you won't modify this template. 

Then execute the following commands :

```bash
git clone https://github.com/<your-username>/php-framework

cd php-framework

docker compose up -d --build

docker compose exec php-framework composer install

docker compose exec php-framework composer dump-autoload
```

The application is now available on your machine at `127.0.0.1:8080` . To test if it is working correctly, go to `127.0.0.1:8080/test`. A page with `Test Controller` written on it should be displayed.

## Objectives

### What this application is about

This is a simple framework writtent in PHP and using OOP. It contains a router, allowing you to redirect specific HTTP request to a dedicated controller in your code.

#### How it works

In order to create a new route, add its configuration in the `/app/config/routes.json` . You can reuse the template of the `/test` route. You can adjust the route `uri`, what `HTTP methods` are accepted, and what `controller` will be used to handle the request.

A controller must extends the `App/Controller/AbstractController` class. It must be saved in the `app/src/Controllers` folder. You can save a controller in a subfolder inside it.

### How to submit your solutions

Once you finished your exercice, you will determine what is your final branch. From there, you will create a Pull Request to this repository main branch :

![PR button](assets/PR.png)

The pull request should have in its title your firstname and your lastname.

## Exercices

You will use this template to build a json API in order to store, fetch, update and delete contact forms.

The routes will only accept data in the `application/json` format. 

Use the `header` of the request to verify this format. 

Use the `header` in the response to apply this format.

### Exercice 1 /5

Create a new route to store a contact form.

The route should be accessible at the uri `/contact`. It should only accept `POST` method.

In the request body, it should only allows `email`, `subject` and `message` properties. Like the following :

```json
{
  "email": "leia@alderaan.com",
  "subject": "Help me Obi Wan",
  "message": "You are my only hope"
}
```

Any other property is not accepted, and the controller shoudl respond with a 400 status error.

If the request body is valid, save the contact form into a file located in `/app/var/contacts`. The file should have a name following this template : `timestamp_email.json`. For example : `1731245562_leia@alderaan.json`.

If should have the following content template :

```json
{
  "email": "email",
  "subject": "subject",
  "message": "message",
  "dateOfCreation": "timestamp",
  "dateOfLastUpdate": "timestamp"
}
```

Then, send a response with a 201 status and the following content template :

```json
{
  "file": "yyyy-mm-dd_hh-ii-ss_email.json"
}
```

### Exercice 2 /3

Create a new route to fetch all contact forms precedently stored.

The route should be accessible at the uri `/contact`. It should only accept `GET` method.

Send a response with a 200 status and the content of all the contact forms saved, as the following content template : 

```json
[
  {
    "email": "leia@alderaan.com",
    "subject": "Help me Obi Wan",
    "message": "You are my only hope",
    "dateOfCreation": "timestamp",
    "dateOfLastUpdate": "timestamp"
  },
  {
    "email": "han@solo.com",
    "subject": "Yes your highness",
    "message": "I know",
    "dateOfCreation": "timestamp",
    "dateOfLastUpdate": "timestamp"
  },
  ...
]
```

### Exercice 3 /5

Create a new route to fetch a specific contact forms precedently stored.

The route should be accessible at the uri `/contact`. It should only accept `GET` method.

Using the uri, find a way for the user to specifiy which contact form they want to get. 

If the contact form is not found, send a response with a 404 status error.

If the contact form is found, send its content in a response with a 200 status :

```json
{
  "email": "han@solo.com",
  "subject": "Yes your highness",
  "message": "I know",
  "dateOfCreation": "timestamp",
  "dateOfLastUpdate": "timestamp"
}
```

### Exercice 4 /4

Create a new route to update a specific contact forms precedently stored.

The route should be accessible at the uri `/contact`. It should only accept `PATCH` method.

Using the uri, find a way for the user to specifiy which contact form they want to update. 

If the contact form is not found, send a response with a 404 status error.

The user can only update the values of 
- email
- subject
- message

They can update one or more value at the same time.

If they try to update anything else, send a response with a 400 error. For example : 

```json
{
  "email": "leia@rebellion.com",
  "subject": "new subject",
  "message": "new message"
}
```

Is valid.

```json
{
  "email": "leia@rebellion.com",
}
```

Is valid.

```json
{
  "email": "leia@rebellion.com",
  "subject": "new subject",
  "message": "new message",
  "recipient": "r2d2@robot.com"
}
```

Is invalid.

If the request body is valid, update the values in the corresponding file. You will also update the value of `dateOfLastUpdate`.

Then, send a response with a 200 status and the following content template :

```json
{
  "email": "email",
  "subject": "subject",
  "message": "message",
  "dateOfCreation": "timestamp",
  "dateOfLastUpdate": "timestamp"
}
```

### Exercice 5 /3

Create a new route to delete a specific contact forms precedently stored.

The route should be accessible at the uri `/contact`. It should only accept `DELETE` method.

Using the uri, find a way for the user to specifiy which contact form they want to delete. 

If the contact form is not found, send a response with a 404 status error.

If the contact for is found, delete the file. Then send a response with a 204 status code and no content.

#### Bonus points

##### uri pattern +4

For the exercices 3, 4 and 5, find a way to use the following uri pattern to fetch the contact form :

`/contact/<filename>`

##### Git repository +2

- working with branches
- doing atomic commits using [conventional commits](https://www.conventionalcommits.org/en/v1.0.0/)
- doing PR or merges from your working branch to your main branch once a task is completed