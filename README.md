GAMeasurementProtocol-PHP
=========================

track events from the server in Google Analytics from PHP

    $ga = new GAMeasurementProtocol('UA-{yourcode}', 'yourname.com');  // Tracking to Google Analytics
    $ga->trackPageView('/{url_to_track}/','{your page title}');
  
  Or
  
    $ga->trackEvent('event_catgeory','action');

  Or
  
    $ga->trackEvent('event_catgeory','action','optional-label','optiona-value');
