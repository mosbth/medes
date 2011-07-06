Install a new site.

1. Create a config.php

$ cp config-sample.php config.php
Edit it if needed.

2. Create a database

sqlite> create table if not exists pp(module text, key text, value text, primary key(module, key));
sqlite> insert into pp (module, key) values ('CPrinceOfPersia', 'config');
http://www.anderspbygg.se/m2/site/install_database.php
