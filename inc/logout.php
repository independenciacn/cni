<?php
    session_start();
	session_destroy();
	header("Location:../index.php?exit=0");
	exit(0);
