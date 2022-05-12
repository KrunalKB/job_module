<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registration Form</title>
    <style>
      body {
        background-color: black;
      }

      * {
        box-sizing: border-box;
      }

      /* Add padding to containers */
      .container {
        padding: 16px;
        background-color: white;
      }

      /* Full-width input fields */
      input {
        width: 100%;
        padding: 15px;
        margin: 5px 0 22px 0;
        display: inline-block;
        border: none;
        background: #f1f1f1;
      }

      /* Overwrite default styles of hr */
      hr {
        border: 1px solid #f1f1f1;
        margin-bottom: 25px;
      }

      /* Set a style for the submit button */
      .registerbtn {
        background-color: #04aa6d;
        color: white;
        padding: 16px 20px;
        margin: 8px 0;
        border: none;
        cursor: pointer;
        width: 100%;
        opacity: 0.9;
      }

      .registerbtn:hover {
        opacity: 1;
      }
    </style>
  </head>
  <body>
    <form action="">
      <div class="container">
        <p>Please fill in this form to create an account.</p>
        <hr />

        <label for="username"><b>Username</b></label>
        <input
          type="text"
          placeholder="Enter username"
          name="username"
          id="username"
          required
        />

        <label for="email"><b>Email</b></label>
        <input
          type="email"
          placeholder="Enter Email"
          name="email"
          id="email"
          required
        />

        <label for="fname"><b>First name</b></label>
        <input
          type="text"
          placeholder="Enter first name"
          name="fname"
          id="fname"
          required
        />

        <label for="lname"><b>Last name</b></label>
        <input
          type="text"
          placeholder="Enter last name"
          name="lname"
          id="lname"
          required
        />

        <label for="psw"><b>Password</b></label>
        <input
          type="password"
          placeholder="Enter Password"
          name="psw"
          id="psw"
          required
        />

        <hr />

        <button type="submit" class="registerbtn">Register</button>
      </div>
    </form>
  </body>
</html>
