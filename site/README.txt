Install a new site. Go to the site directory and do:

1. Create a setup.php

$ cp setup-sample.php_ setup.php
Edit the settings array if needed.

2. Point webbrowser to setup.php

Do as it say.

Point to setup.php?update to save updated configuration array to database.

3. Create a user in the database.

For example: sqlite> insert into user (account, email, password, algorithm) values ('adm', 'adm@dbwebb.se', 'adm', 'plain');
