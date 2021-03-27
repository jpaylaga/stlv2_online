<?php
use App\Events\BetAdded;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::group(['middleware' => ['auth']], function () {

    Route::get('/player_dashboard', 'PlayerDashboardController@index')->name('player-dashboard');

    Route::get('/', 'DashboardController@index')
        ->middleware('can:manage-users')
        ->name('admin-dashboard');

    Route::get('/user-profile', 'UserController@showProfile');
    Route::get('/manage-areas', 'AreaController@manage')
        ->middleware('can:manage-areas');

    Route::get('/manage-users/{any?}', 'UserController@index')
        ->middleware('can:manage-users')
        ->where('any', '.*');

    Route::get('/manage-coordinators/{any?}', 'UserController@manageCoordinators')
        ->middleware('can:manage-users')
        ->where('any', '.*');

    Route::get('/manage-players/{any?}', 'UserController@managePlayers')
        ->middleware('can:manage-users')
        ->where('any', '.*');
    
    Route::get('/manage-tellers/{any?}', 'UserController@manageTellers')
        ->middleware('can:manage-users')
        ->where('any', '.*');

    Route::get('/manage-area-admins/{any?}', 'UserController@manageAreaAdmins')
        ->middleware('can:manage-users')
        ->where('any', '.*');

    Route::get('/manage-bets/{any?}', 'BetController@index')
        ->middleware('can:manage-bets')
        ->where('any', '.*');

    Route::get('/transactions/{any?}', 'TransactionController@index')
        ->where('any', '.*');

    Route::get('/cancelled-tickets/{any?}', 'TicketController@cancelledTickets')
        ->where('any', '.*');

    Route::get('/draw-results/{any?}', 'DrawController@index')
        ->middleware('can:draw-results')
        ->where('any', '.*');

    Route::get('/winners/{any?}', 'WinnerController@index')
        ->middleware('can:view-winners')
        ->where('any', '.*');

    Route::get('/payouts', 'PayoutController@index');

    Route::get('/summary-sales', 'ReportsController@summarySales');
    Route::get('/summary-report', 'ReportsController@summary');
    Route::get('/reports-coordinators/{any?}', 'ReportsController@reportsCoordinators')
        ->where('any', '.*');
    Route::get('/area-summary', 'ReportsController@areaSummary');
    Route::get('/highest-bet', 'ReportsController@highestBet');
    Route::get('/hot-numbers', 'ReportsController@hotNumbers');
    // Route::get('/hot-numbers-multiple', 'ReportsController@hotNumbersMultiple');

    Route::prefix('settings')->group(function () {
        Route::get('/bet-limits', 'ReportsController@betLimits');
        Route::get('/payout-rates', 'ReportsController@payoutRates');
        Route::get('/area-settings', 'AreaController@settings')->name('area-settings');
    });
    
    Route::get('/outlets/{any?}', 'OutletController@index')
            ->where('any', '.*');

    Route::group(['middleware' => ['can:transfer-credits']], function () {
        Route::get('/credits', 'CreditsController@index');
        Route::get('/credit-requests', 'CreditRequestController@index');
    });

    Route::group(['middleware' => ['can:god-mode']], function () {
        Route::get('/manage-games', 'GameController@index');
    });

    Route::get('/regenerate-token/{user}', 'UserController@regenerateToken')->name('regenerate-token');

    Route::prefix('play')->group(function () {
        Route::get('/games', 'GameController@listGames');
        Route::get('/stl', 'GameController@playStl');
    });

});

Route::get('/register', 'Auth\RegisterController@index')->name('online-register');
Route::post('/web-register', 'Auth\RegisterController@create')->name('web-register');
Route::get('/thank-you/{user}', 'Auth\RegisterController@thankYou')->name('thank-you');

Route::get('/test', function(){
    var_dump( env('ENABLE_AUTODIVIDE') );
});
