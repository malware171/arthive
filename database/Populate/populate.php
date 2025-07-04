<?php

require __DIR__ . '/../../config/bootstrap.php';

use Core\Database\Database;
use Database\Populate\CategoryPopulate;
use Database\Populate\UserPopulate;

Database::migrate();
UserPopulate::populate();
CategoryPopulate::populate();
