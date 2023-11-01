<?php include_once("header.php")?>

<div class="container">
<h2 class="my-3">Register new account</h2>

<!-- Create auction form -->
<form method="POST" action="process_registration.php">
  <div class="px-2">
    <div class="form-group">
      <label for="email">Email</label>
      <input type="email" class="form-control" id="email_input" name="email" placeholder="Email" oninput="checkForm()">
      <small hidden id="emailHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
    </div>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="username">Username</label>
        <input type="text" class="form-control" id="username" name="username" placeholder="Username" oninput="checkForm()">
        <small hidden id="usernameHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
      </div>
      <div class="form-group col-md-6">
        <label for="display_name">Display Name</label>
        <input type="text" class="form-control" id="display_name" name="display_name" placeholder="Display Name" oninput="checkForm()">
        <small hidden id="dispNameHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
      </div>
    </div>
    
    <div class="form-group">
      <label for="password">Password</label>
      <input type="password"  class="form-control" id="password_input" name="password" placeholder="Password" oninput="checkForm()">
      <small hidden id="pwdHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
    </div>
    <div class="form-group">
      <label for="passwordConfirmation">Password Confirmation</label>
      <input type="password"  class="form-control" id="passwordConfirmation" name="passwordConfirmation" placeholder="Password" oninput="checkForm()">
      <small hidden id="pwdHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
    </div>
    <div class="form-group row">
    <div class="col-sm-4">Agree to enable email subscription</div>
    <div class="col-sm-8">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="opt_in_email" name="opt_in_email">
        <label class="form-check-label" for="gridCheck1">
          Agree
        </label>
      </div>
    </div>
  </div>
    <div class="form-group row px-3">
      <button id="submitBtn" type="submit" class="btn btn-primary form-control">Register</button>
    </div>
  </div>
</form>

<div class="text-center">Already have an account? <a href="" data-toggle="modal" data-target="#loginModal">Login</a></div>

<?php include_once("footer.php")?>

<script type="text/javascript">

  document.addEventListener('DOMContentLoaded', function () {
    checkForm();
  });

  function validateMatchPassword() {
    let password = document.getElementById("password_input").value;
    let confirmPassword = document.getElementById("passwordConfirmation").value;
    return password === confirmPassword
  }

  function checkForm() {    
    if (document.getElementById("email_input").value && document.getElementById("username").value 
      && document.getElementById("display_name").value && document.getElementById("password_input").value
        && document.getElementById("passwordConfirmation").value && validateMatchPassword()) {
          document.getElementById("submitBtn").disabled = false;
    } else {
      document.getElementById("submitBtn").disabled = true;
    }
  }
</script>
