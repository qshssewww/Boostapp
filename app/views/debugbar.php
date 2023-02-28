<?php

use App\Utils\DebugBar;

$dbQueries = DebugBar::getLast();
?>
    <style>
    .debug-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 9999;
        height: 200px;
        background: #f4f4f4;
        border-top: 2px solid #ff650b;
        overflow-y: auto;
        transform: translateZ(0);
    }

    .debug-bar pre {
        width: 90%;
        max-width: 1200px;
        white-space: normal;
    }

    .debug-bar .hljs-keyword {
        text-transform: uppercase;
    }
</style>

<div class="debug-bar pt-10" dir="ltr">
    <div class="container-fluid">
        <div class="row mb-10">
            <div class="col-sm-4">
                <button type="button" class="btn btn-info js-number-of-queries">
                    Database <span class="badge badge-light"><?= count($dbQueries) ?></span>
                </button>
            </div>
            <div class="col-sm-4 text-center">
                <i class="fad fa-chevron-up mb-5 js-expand-debug-bar" style="font-size: 22px; cursor: pointer;"></i>
                <script>
                    $(document).ready(function () {
                        $('.js-expand-debug-bar').click(function () {
                            if ($(this).hasClass('fa-chevron-up')) {
                                $('.debug-bar').animate({height: '600px'});
                                $('.js-expand-debug-bar').removeClass('fa-chevron-up').addClass('fa-chevron-down');
                            } else {
                                $('.debug-bar').animate({height: '200px'});
                                $('.js-expand-debug-bar').addClass('fa-chevron-up').removeClass('fa-chevron-down');
                            }
                        });

                        $('body').on('change', 'select[name=debug-list]', function () {
                            var key = $(this).val();

                            $('.js-debug-table').find('tbody').empty();

                            $.ajax({
                                url: '/office/route.php',
                                type: 'GET',
                                data: {
                                    action: 'debug-bar/get-debug-info',
                                    key: key
                                },
                                success: function (data) {
                                    $('.js-debug-table').find('tbody').html(data.table);

                                    $('.js-number-of-queries').find('span').text(data.count);

                                    $('.debug-bar').animate({ scrollTop: 0 }, 'slow');
                                }
                            })
                        });

                        $('.js-change-debug-mode').click(function () {
                            var btn = $(this);

                            $.ajax({
                                url: '/office/route.php',
                                type: 'POST',
                                data: {
                                    action: 'debug-bar/set-debug-mode',
                                    mode: btn.attr('data-mode')
                                },
                                success: function (data) {
                                    btn.attr('data-mode', btn.attr('data-mode') == 1 ? 0 : 1);

                                    btn.removeClass('btn-success')
                                        .removeClass('btn-danger')
                                        .addClass(btn.attr('data-mode') == 0 ? 'btn-danger' : 'btn-success');

                                    btn.find('span').text(btn.attr('data-mode') == 0 ? 'Disable' : 'Enable');
                                }
                            });

                            return false;
                        });
                    })
                </script>
            </div>
            <div class="col-sm-4 text-right">
                <div class="d-inline-block w-50 mr-5">
                    <select name="debug-list" id="" class="custom-select w-100">
                        <?php foreach (DebugBar::getKeys() as $keyInfo) { ?>
                            <option value="<?= $keyInfo['key'] ?>" <?= DebugBar::getDebugKey() === $keyInfo['key'] ? 'selected' : '' ?>><?= $keyInfo['url'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <a class="btn btn-sm btn-<?= DebugBar::isEnabled() ? 'danger' : 'success' ?> js-change-debug-mode" href="#!" data-mode="<?= (int)!DebugBar::isEnabled() ?>">
                    <span><?= DebugBar::isEnabled() ? 'Disable' : 'Enable' ?></span> Debug Mode
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm js-debug-table">
                        <thead class="thead-light">
                        <tr>
                            <th>#</th><th>Time</th><th>Type</th><th>Query</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (!empty($dbQueries)) {
                            echo View::make('debugbar/_table', ['dbQueries' => $dbQueries])->render();
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet"
          href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.5.0/styles/default.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.5.0/highlight.min.js"></script>
    <script>hljs.highlightAll();</script>
</div>
