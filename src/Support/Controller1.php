<?php

/**
 * Class Controller1
 *  All Controller Class must Extend this, Model is also extending this which means, Model Can contain Controller function as Well...
 *  The Only Different between This and Api1 class is that, Controller get validate automatically just by putting <input name='token' value="{{ token() }}" type="hidden" /> or simply call form_token() in the form field
 */
abstract class Controller1 extends Api1
{
    /**
     * ...
     * More Features in future
     */
}
