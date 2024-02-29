<?php
namespace aic;

 unset($_SESSION);
 session_destroy();
 header('Location:?do=aic_home');    
