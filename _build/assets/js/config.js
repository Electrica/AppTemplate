requirejs.config({
    baseUrl: '/assets/components/apptemplate/js/web/',
    urlArgs: 'v=' + document.head.querySelector('meta[name="assets-version"]').content,
    waitSeconds: 30,
    paths: {
        jquery: 'lib/jquery.min',
        bootstrap: 'lib/bootstrap.min',
        pdopage: 'lib/pdopage.min',
    },
    shim: {
        bootstrap: {
            //deps: ['jquery', 'popper']
            deps: ['jquery']
        },
        pdopage: {
            deps: ['jquery'],
            exports: 'pdoPage'
        },
        apptemplate: {
            deps: ['jquery', 'bootstrap'],
            exports: 'AppTemplate'
        },
    }
});
/*
requirejs.onError = function (err) {
    if (err.requireType === 'timeout') {
        if (typeof AppTemplate === 'object') {
            AppTemplate.Message.alert('Could not load javascript. Try to reload page.', function () {
                document.location.reload();
            })
        } else {
            alert('Could not load javascript. Try to reload page.');
            console.log(err);
        }
    } else {
        throw err;
    }
};*/