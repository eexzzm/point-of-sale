<?php

//Handler for print profile

function print_profile()
{
  //Use SESSION
  $fullname = $_SESSION["fullname"];
  $role = $_SESSION["role"];
  echo "
      <h3>Hello, $fullname 👏</h3>
        <p class=\"text-muted\">You are logged in as $role</p>
  ";
}