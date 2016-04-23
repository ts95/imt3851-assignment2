Assignment 2 in IMT3851 (Web programming II)
===
Solution for the second assignment in IMT3851 (Web programming II).

-- Work in progress --

# Database setup
By default the project will create the database and the tables automatically
upon the first request. You do not need to create it yourself (and you shouldn't).
Database settings are specified in `includes/settings.php`.

# Serving from a subdirectory
If the project isn't served from the root directory of the webserver, but
rather a subdirectory, this needs to be specified in the `includes/settings.php`
file. Set `ROOT_PATH` to point to the project directory.