<?
//initialize session
session_start();
?>
<div class="navbar navbar-inverse nav">
    <div class="navbar-inner">
        <div class="container">
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        </a>
        <a class="brand" href="#">Oracle Reporting System</a>
            <div class="nav-collapse collapse">
            
                <ul class="nav">
                    <li class="divider-vertical"></li>
                    <li><a href="main.php"><i class="icon-home icon-white"></i> Home</a></li>
                </ul>
            
                <div class="pull-right">
                    
                    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">Welcome! 
					<span class="add-on"><i class="icon-user"></i></span>
					<?php
                    echo $userAcess['fullName'];?> <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="#">Change Password</a></li>
                        <li class="divider"></li>
                        <li><a href="logoutUser.php">Log-out</a></li>
                        
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>