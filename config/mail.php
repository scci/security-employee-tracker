<?php

return array(
  "driver" => "smtp",
  "host" => "mailtrap.io",
  "port" => 2525,
  "from" => array(
      "address" => "from@example.com",
      "name" => "Toni Donaghue"
  ),
  "username" => "1c70425a4e2ab0",
  "password" => "915e7ba7fb98a2",
  "sendmail" => "/usr/sbin/sendmail -bs",
  "pretend" => false
);
