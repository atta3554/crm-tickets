add these lines in last sections of your wp-config.php file:

define(CLIENT_ID, 'your_crm_client_id');
define(CLIENT_SECRET, 'your_crm_client_secret');
define(CLIENT_USERNAME, 'your_crm_client_username');
define(CLIENT_PASSWORD, 'your_crm_client_password');

these are required constants during plugin execution and should be available during plugin workflow