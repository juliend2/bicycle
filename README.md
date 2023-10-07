# WARNING: DON'T USE THIS.
## This is an old codebase with many critical security vulnerabilities.


* * *

<br/><br/><br/><br/><br/><br/>

Bicycle
=======

The command line interface
--------------------------

Bicycle includes a (Ruby) Command-line interface, which is used to generate valid schema migrations.

To install it, add the cli folder into your shell $PATH, for example:

    export PATH="$PATH:/Applications/XAMPP/htdocs/lib/bicycle/bin"

This assumes that you have a centralized copy of the bicycle repository for all
your bicycle apps.

How to use Bicycle
------------------

First, define a BASE_PATH (used by url_for and link_to):

    define('BASE_PATH', 'http://localhost');

Then, include this file:

    include_once 'bicycle/all.php';


License
-------

Bicycle is released under the MIT license.

