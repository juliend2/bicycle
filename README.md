Bicycle
=======

The command line interface
--------------------------

Bicycle includes a (Ruby) Command-line interface, which is used to generate valid schema migrations.

To install it, add the cli folder into your shell $PATH, for example:

    export PATH="$PATH:/Applications/XAMPP/htdocs/lib/bicycle/cli"

This assumes that you have a centralized copy of the bicycle repository for all
your bicycle apps.

How to use Bicycle
------------------

First, define a BASE_PATH (used by url_for and link_to):

    define('BASE_PATH', 'http://localhost');

Then, include this file:

    include_once 'bicycle/all.php';


Motivations
-----------

I want routes that lead to views without the burden of a Controller. And from 
the views, query the models directly.

I want something i can install on the dirtiest, cheapest, low-end hosting
(where SSH is not an option).

I want schema migrations, without the command-line (running them via an 'admin' 
section).

I want clean URLs, but i want my sites to still work without ModRewrite.

I want to create sites that can run on PHP4.


I want something for the dirty roads.

License
-------

Bicycle is released under the MIT license.

