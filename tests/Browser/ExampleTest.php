<?php

use Laravel\Dusk\Browser;

test('the central welcome page loads', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('http://saas.test/')
            ->assertSee('Laravel');
    });
});
