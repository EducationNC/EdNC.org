# ed-nc

EdNC.org
EdNC is an open source publication platform built on WordPress, Roots, and a myriad of plugins.

Source: https://github.com/EducationNC/EdNC
Homepage: https://www.ednc.org/
Contributing
Everyone is welcome to help contribute and improve this project. There are several ways you can contribute:

Reporting [issues] (https://github.com/EducationNC/EdNC/issues)
Suggesting new features
Writing or refactoring code
Fixing issues
Theme features
Grunt for compiling SASS to CSS, checking for JS errors, live reloading, concatenating and minifying files, versioning assets, and generating lean Modernizr builds
Bower for front-end package management
HTML5 Boilerplate
The latest jQuery via Google CDN, with a local fallback
The latest Modernizr build for feature detection, with lean builds with Grunt
An optimized Google Analytics snippet
Bootstrap
Organized file and template structure
ARIA roles and microformats
Theme activation
Theme wrapper
Cleaner HTML output of navigation menus
Posts use the hNews microformat
Multilingual ready and over 30 available community translations
Installation
This project does not include WordPress, so you will need to first download and install WordPress in your project directory.

Clone the git repo - git clone git://github.com/EducationNC/EdNC.git - or download it and then place the files into your project directory.

If you don't use Bedrock, you'll need to add the following to your wp-config.php on your development installation:

define('WP_ENV', 'development');
Theme activation
Reference the theme activation documentation to understand everything that happens once you activate Roots.

Theme development
Roots uses Grunt for compiling SASS to CSS, checking for JS errors, live reloading, concatenating and minifying files, versioning assets, and generating lean Modernizr builds.

Pleaes refer to the Roots [README] (https://github.com/roots/roots-sass/blob/master/README.md) for more information on setting up your local dev environment to develop using Grunt.

Documentation
Roots 101 — A guide to installing Roots, the files, and theme organization
