

<!-- //usage -->

Route::prefix('dashboard/claims')->middleware(['auth','checkIsViewer'])->group(function () {

}