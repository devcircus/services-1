# Changelog

All notable changes to perfect-oblivion/services will be documented in this file

## 0.1.0 - 04/03/2019

-   initial release

## 0.2.0 - 05/12/2019

-   Add queued services.
    - Use the 'queue' method instead of 'call' to queue the service instead of calling immediately.
    - Nothing has changed with how the Service class itself is constructed. The only api change is the
      addition of the 'queue' method.
