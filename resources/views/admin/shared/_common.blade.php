<input type="hidden" 
       id="action_cable_js_config" 
       value="1" 
       data-env="{{ config('app.env') }}"
       data-action-cable-host="{{ config('broadcasting.connections.pusher.options.host', config('app.url')) }}">