# ZSO10 App – Web
Web API for ZSO10 App

## License
Entire ZSO10 App project is licensed uder GNU GPLv3, whose copy is provided
in every package in file LICENSE.

## Basic ZSO10 App project rules
1. For convenience all code documentation for the entire ZSO10 App project should be
written in English. Of course all additional text files (as this README) can
exist in different languages, but English is mandatory. Language code should be
included in file name, for example:
    - README.md – default, English file
    - README-pl.md – additional, Polish file
2. If it's possible and reasonable, 100 character margin should be preserved in
all files. In code files comments can exceed this limit.
3. Code rules:
    - Java-like braces
    - 4-space indents
    - single quotes for PHP's strings and double quotes for XML/SQL
4. Documentation and comments:
    - Code developed in this project is also directed for educational purpose,
      so please provide comprehensive documentation and comments to improve
      understandability.
    - for specific files please use it's documentation standard:
        - **PHP** – [phpDocumentor](http://www.phpdoc.org/)
        - **Java** – [JavaDoc](http://www.oracle.com/technetwork/articles/java/index-137868.html)
        - **C++** – [Doxygen](http://www.stack.nl/~dimitri/doxygen/)
    - in-code tags:
        - **TODO** – with description points some specific thing to do in the
          future. It's also possible to include priority, as "LOWPRIO"
        - **TODEBUG** – points place or file, which needs to be debugged
        - **TORETHINK**/**TOREWRITE** – points algorithm which should be rewritten
    - in folder/project root can also be file TODO, with list of folder/project
      specific things to do with highest priority on top
    - commit description should express brief look at changes. Full description
      can be provided both with text description or list of changes:
        - **+** means that something was added
        - **-** means that something was removed
        - **~** means that something was changed
        - **D** means that something was debugged
5. Editors:
    - PHP and C++ code is primarily written using Nebeans IDE, but it could be
      replaced with whatever you want.
    - Android app is written using Android Studio. Besides Java source files,
      there are provided Android Studio project files, so for convenience please
      use it for developing Android app.
6. Please keep in mind, that this project is open source, therefore every
contributor should at least have basic idea of open source software
([Wiki](http://en.wikipedia.org/wiki/Open-source_software)) and
GNU GPLv3 license ([Wiki](http://en.wikipedia.org/wiki/GNU_General_Public_License)),
which is used for this project.
7. Anyone, who wants to actively contribute to this project should get in touch
with @MarPiRK, but of course anyone can submit issues on GitHub pages.
8. Current TODO list and roadmap is publicly available on
[Trello](https://trello.com/b/kLHUx3Uk).
9. All user exposed pieces of ZSO10 App should be compulsorily provided in
Polish and English localizations, but anyone, who wants to, can translate
locale files to whichever language he wants.
