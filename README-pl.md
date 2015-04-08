# ZSO10 App – Web
Internetowe API dla ZSO10 App

## Licencja
Cały projekt ZSO10 App jest udostępniony pod licencją GNU GPLv3, której kopia
znajduje się w pliku LICENSE-pl.

## Ogólne zasady projektu ZSO10 App
1. Dla wygody, cała dokumentacja kodu projektu ZSO10 App powinna być napisana
w języku angielskim. Oczywiście dodatkowe pliku (tak jak README) mogą istnieć
w innych językach, jednak trzeba pamiętać, że angielski jest obowiązkowy.
Do nazw plików powinien być dołączony kod języka, np.:
    - README.md – domyślna, angielska wersja
    - README-pl.md – dodatkowa, polska wersja
2. Jeżeli to możliwe i rozsądne, powinien zostać zachowany 100-znakowy margines
dla wszystkich plików. W plikach źródłowych można go przekroczyć dla komentarzy.
3. Zasady formatowania kodu:
    - Javowe nawiasy
    - 4-spacjowe wcięcia
    - pojedynczy cudzysłówn dla tekstów PHP, podwójny cudzysłów dla XML/SQL
4. Dokumentacja i komentarze:
    - Kod tego projektu jest tworzony również w celach naukowych. Z tego powodu
      proszę umieszczać pełną dokumentację i komentarze w celu polepszenia
      zrozumiałości kodu.
    - dla plików w danym języku proszę stosować odpowiedni dla niego standard
      dokumentacji::
        - PHP – [phpDocumentor](http://www.phpdoc.org/)
        - Java – [JavaDoc](http://www.oracle.com/technetwork/articles/java/index-137868.html)
        - C++ – [Doxygen](http://www.stack.nl/~dimitri/doxygen/)
    - tagi w kodzie:
        - TODO – z opisem pokazuje konkretną rzecz do zrobienia w danym miejscu.
          Można również dołączyć priorytet, np. "LOWPRIO"
        - TODEBUG – miejsce, które trzeba zdebugować
        - TORETHINK/TOREWRITE – miejsce/algorytm, który powinien zostać
          przepisany od nowa
    - w folderze może zostać również umieszczony plik TODO, z listą konkretnych
      dla danego folderu/projektu rzeczy do zrobienia z najwyższym priorytetem
      u góry
    - opis commitu powinien pokazywać ogólne zmiany. Pełny opis może zawierać
      opis w postaci tekstu ciągłego lub listę mian:
        - **+** oznacza, że coś zostało dodane
        - **-** oznacza, że coś zostało usunięte
        - **~** oznacza, że coś zostało zmienione
        - **D** oznacza, że coś zostało zdebugowane
5. Edytory:
    - Kod napisany w PHP i C++ jest pisany głownie w IDE Nebeans, ale nic nie
      stoi na przeszkodzie, żeby używać innego.
    - Aplikacja na Androida jest pisana w Android Studio. Oprócz plików
      źródłowych Javy, w repozytorium są zawarte pliki projektu Android Studio,
      więc dla wygody proszę używać tego IDE.
6. Proszę pamiętać, że cały projekt jest otwartoźródłowy, dlatego jego
uczestnicy powinni posiadać ogólną wiedzę na temat oprogramowania open source
([Wiki](http://pl.wikipedia.org/wiki/Otwarte_oprogramowanie)) i licencji
GNU GPLv3 ([Wiki](http://pl.wikipedia.org/wiki/GNU_General_Public_License)
używanej w tym projekcie.
7. Każdy, kto chciałby aktywnie uczestniczyć w projekcie, powinien skontaktować
się z użytkownikiem @MarPiRK. Oczywiście każdy może zgłosić buga na stronach
GitHuba.
8. Aktualna lista zadań i harmonogram działania jest dostępny publicznie na
[Trello](https://trello.com/b/kLHUx3Uk).
9. Wszystkie części widoczne dla użytkownika końcowego w aplikacjach powinny
posiadać obowiązkowo polską i angielską lokalizację. Oczywiście każdy może
przetłumaczyć pliki językowe na jakikolwiek język.
