# PHPUnit in Docker and PHPStorm
This isn't very well documented on the PHPStorm website, and I only found out
about it through this thread:
https://intellij-support.jetbrains.com/hc/en-us/community/posts/115000455850-PHPUnit-use-Composer-autoloader-could-not-parse-PHPUnit-version-output- 

### The TL;DR 
To get PHPUnit in a Docker container and PHPStorm to play nicely you must
manually enter the full path to the PHPUnit folder (as it is in the container)
into the path to script box in the PHPStorm settings window when you add the
interpreter. This is found in Settings > Languages & Frameworks > PHP > 
Test Frameworks.

You will also need to specify the bootstrap file's path in the Test Runner 
section of the framework configuration, again as the path appears in the 
container.


It is highly probable that you will need to do the same thing for Codeception
too, although for me PHPStorm worked out where that was once it knew where 
PHPUnit was...

### Warning!
PHPStorm _may_ change the path mappings in the settings to read /opt/project
on upgrade, as per 
[this](https://intellij-support.jetbrains.com/hc/en-us/community/posts/115000455850/comments/360000464500)
post - if it all stops working check that.
