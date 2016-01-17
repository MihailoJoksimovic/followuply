# Followuply Box

Hello stranger! This is a Followuply project made with love by Mihailo Joksimovic and Aleksandar Ikonic.

Fastest way to start developing is to use vagrant.

Run the following commands in order to get your environment up & running:

+ vagrant up

When provisioning has finished, you should be able to access the project by visiting http://192.168.33.11/index_dev.php or http://192.168.33.11/

You can build the DB by navigating to /var/www and running ``php bin/console orm:schema-tool:update --dump-sql --force``

Enjoy!`
