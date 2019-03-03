<nav class="navbar navbar-inverse">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-nav" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="moderators.php"><?php echo "Moderators"; ?> </a>
    </div>
    <div class="collapse navbar-collapse" id="app-nav">
      <ul class="nav navbar-nav">
        <li><a href="managers.php"><?php echo "Managers" ?></a></li>

   
      </ul>
      <ul class="nav navbar-nav">
        <li><a href="members.php"><?php echo "Members" ?></a></li>
        
   
      </ul>
    
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $_SESSION['Username']; ?><span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="managers.php?do=Edit&userid=<?php echo $_SESSION["ID"]; ?>"><?php echo lang("edit_profile_admin"); ?></a></li>
            <li><a href="logout.php"><?php echo lang("Logout_admin"); ?></a></li>
        
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>