LICENSE

This Source Code Form is subject to the terms of the Mozilla Public
License, v. 2.0. If a copy of the MPL was not distributed with this
file, You can obtain one at https://mozilla.org/MPL/2.0/.

TEST

Please try autotheme.de/examples/finicky.php with Google Chrome.
Finicky is just a simple of fork of the game Yatzy and Finicky is showing a little proof of concept for the Autotheme Framework.

First press New Game. Then Throw the bucket. Then you have 2 more tries for a round to throw dices back in the bucket or to enter a score in the list by click. After click next round after all 6 categories are full.

As you can look up also by clicking in the browser for source code there is not any line of code for the logic of the game. The processing of the logic is happening on the server side while most the logic of the most online computer web games is processed on the client side. This is because the Autotheme Framework sends events with data to the browser and if the server resends its request back it just sends items, which have to be replaced. This is because the server has something like a complete copy of the app and just sends the difference of the HTML-Code to the client; the client now just can replace seamlessly the new items without any manual coding of AJAX so that classicaly the user does not notice that a site will be reloaded and gets the desktop feeling.
