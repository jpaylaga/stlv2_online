<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['json.response']], function () {
    
    // private routes
    Route::middleware('auth:api', 'can:make-requests')->group(function () {

        Route::get('/users', 'UserController@get');
        Route::post('/user/add', 'UserController@store');
        Route::get('/user', 'UserController@getLoggedInUser');
        Route::get('/user/{user}', 'UserController@getUser')
            ->middleware('can:manage-users');
        Route::patch('/user/{user}', 'UserController@update');
        Route::post('/user/change-status', 'UserController@changeStatus');
        Route::post('/user/change-credit-status', 'UserController@changeCreditStatus');
        Route::post('/user/add-image', 'UserController@addImage');
        Route::post('/user/update-percentage', 'UserController@updatePercentage');
        Route::post('/user/update-float', 'UserController@updateFloat');
        Route::post('/confirm_registration', 'Api\RegistrationController@confirm');

        Route::get('/areas', 'AreaController@get');
        Route::get('/area/{area}', 'AreaController@getArea');
        Route::post('/area/add', 'AreaController@store');
        Route::get('/area_with_prices/{area}', 'AreaController@getAreaWithPrices');
        Route::patch('/area/{area}', 'AreaController@update');
        Route::post('/area/add-image', 'AreaController@addImage');

        Route::get('/outlets', 'OutletController@get');
        Route::get('/outlet/{outlet}', 'OutletController@getOutlet');
        Route::post('/outlet', 'OutletController@store');
        Route::patch('/outlet/{outlet}', 'OutletController@update');

        Route::get('/games', 'Api\GameController@get');
        Route::get('/games-draw', 'Api\GameController@getGamesWithDraw');

        Route::get('/transactions', 'Api\TransactionController@get');
        Route::get('/transactions/tickets', 'Api\TransactionController@getTickets');
        Route::post('/transaction', 'Api\TransactionController@store');
            // ->middleware('can:manage-bets');
        Route::get('/transaction/{transaction}', 'Api\TransactionController@getTransaction');

        Route::get('/tickets', 'Api\TicketController@get');
        Route::get('/ticket/{ticket}', 'Api\TicketController@getTicket');
        Route::get('/cancelled-tickets', 'Api\TicketController@getCancelledTickets');
        Route::patch('/ticket/cancel', 'Api\TicketController@cancelTicket');

        Route::middleware('can:draw-results')->group(function () {
            Route::post('/draw', 'Api\DrawController@store');
            Route::get('/tickets-to-check', 'Api\TicketController@getTicketsToCheck');
            Route::get('/check-tickets/{draw}', 'Api\TicketController@checkTicketsByDraw');
        });

        Route::get('/results', 'Api\DrawController@get');
        Route::get('/results-by-game', 'Api\DrawController@getResultsByGame');

        Route::prefix('sales')->group(function () {
            Route::get('/draw-sales', 'Api\SalesController@getDrawSales');
            Route::get('/per-game', 'Api\SalesController@getSalesPerGame');
            Route::get('/summary-sales', 'Api\SalesController@getSummarySales');

            Route::get('/gross-sales-by-draw', 'Api\SalesController@getGrossSalesByDrawTime');
            Route::get('/winnings-by-draw', 'Api\SalesController@getWinningsByDrawTime');
            Route::get('/result-sales', 'Api\SalesController@getResultSales');
        });

        Route::prefix('winners')->group(function () {
            Route::get('/all', 'Api\WinnerController@getAll');
            Route::get('/summary', 'Api\WinnerController@getSummary');
            Route::get('/draw', 'Api\WinnerController@getWinners');

            Route::get('/payouts', 'Api\WinnerController@getPayouts');
            Route::get('/get-winning-ticket', 'Api\WinnerController@getWinner');
            Route::post('/payout', 'Api\WinnerController@payWinner');
            Route::post('/remove', 'Api\WinnerController@deleteDuplicate');
        });

        Route::prefix('reports')->group(function () {
            Route::get('/sales-per-agent', 'Api\ReportsController@getSalesPerAgent');
            Route::get('/sales-per-agent2', 'Api\ReportsController@getSalesPerAgent2');
            Route::get('/coordinators-sales', 'Api\ReportsController@getCoordinatorsTotalSales');
            Route::get('/coordinators-sales-detailed', 'Api\ReportsController@getCoordinatorsDetailed');
            Route::get('/area-sales', 'Api\ReportsController@getAreaTotalSales')
                ->middleware('can:view-area-reports');
            Route::get('/highest-bet', 'Api\ReportsController@getHighestBets')
                ->middleware('can:view-bet-reports');
            Route::get('/hot-numbers', 'Api\ReportsController@getHotNumbers')
                ->middleware('can:view-bet-reports');
            Route::get('/hot-numbers-control', 'Api\ReportsController@getHotNumbersControl')
                ->middleware('can:view-bet-reports');
            Route::get('/hot-numbers-summary', 'Api\ReportsController@getHotNumbersSummary')
                ->middleware('can:view-bet-reports');
            Route::get('/bet-limits', 'Api\ReportsController@getBetLimits')
                ->middleware('can:view-bet-reports');
        });

        Route::prefix('betlimit')->group(function () {
            Route::post('/remove', 'Api\BetLimitController@remove')
                ->middleware('can:draw-results');
            Route::post('/set', 'Api\BetLimitController@store')
                ->middleware('can:view-bet-reports');
        });

        Route::prefix('settings')->group(function () {
            Route::post('/', 'SettingController@postSettings');
            Route::get('/type', 'SettingController@getSettingsByType');
        });

        Route::prefix('credits')->group(function () {
            Route::get('/', 'Api\CreditsController@get');
            Route::get('/history', 'Api\CreditsController@getHistory');
            Route::get('/requests', 'Api\CreditRequestController@get');
            Route::patch('/requests/update-status/{creditrequest}', 'Api\CreditRequestController@updateStatus');

            Route::post('/add', 'Api\CreditsController@add')
                ->middleware('can:transfer-credits');
            Route::post('/transfer', 'Api\CreditsController@transfer')
                ->middleware('can:transfer-credits');
        });

        Route::post('/change-password', 'Api\AuthController@changePassword');
        Route::get('/logout', 'Api\AuthController@logout')->name('logout');

    });

    // public routes
    Route::post('/login', 'Api\AuthController@login')->name('login.api');
    Route::post('/register', 'Api\AuthController@register')->name('register.api');
    Route::post('/registration', 'Api\RegistrationController@register');

});
