/**
 * Laravel WebSocket Client for listening to LoanCreated events
 * Uses pusher-js library (recommended for Laravel WebSockets)
 * 
 * Usage:
 * <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
 * <script src="/js/websocket-client.js"></script>
 * <script>
 *   // Connect and listen
 *   window.loanWebSocket.connect().then(() => {
 *     window.loanWebSocket.on('loan.created', (data) => {
 *       console.log('New loan created:', data);
 *       // Handle the loan data here
 *     });
 *   });
 * </script>
 */

(function() {
  'use strict';

  const WS_CONFIG = {
    host: '127.0.0.1',
    port: 6001,
    appKey: 'local',
    cluster: 'mt1',
  };

  let pusher = null;
  let channel = null;
  let eventListeners = {};

  // Check if Pusher is available
  const isPusherAvailable = typeof Pusher !== 'undefined' || typeof window.Pusher !== 'undefined';

  function triggerEvent(eventName, data) {
    console.log('📢 Event triggered:', eventName, data);
    if (eventListeners[eventName]) {
      eventListeners[eventName].forEach(callback => callback(data));
    }
    // Also trigger 'all' events
    if (eventListeners['all']) {
      eventListeners['all'].forEach(callback => callback(eventName, data));
    }
  }

  function on(eventName, callback) {
    if (!eventListeners[eventName]) {
      eventListeners[eventName] = [];
    }
    eventListeners[eventName].push(callback);
  }

  function off(eventName, callback) {
    if (eventListeners[eventName]) {
      eventListeners[eventName] = eventListeners[eventName].filter(cb => cb !== callback);
    }
  }

  function connect() {
    return new Promise((resolve, reject) => {
      if (channel) {
        resolve();
        return;
      }

      // Use Pusher with Laravel WebSockets configuration
      const wsUrl = `http://${WS_CONFIG.host}:${WS_CONFIG.port}`;
      
      console.log('🔌 Connecting to Laravel WebSockets at:', wsUrl);
      
      // Initialize Pusher for Laravel WebSockets
      const pusherConfig = {
        wsHost: WS_CONFIG.host,
        wsPort: WS_CONFIG.port,
        wssPort: WS_CONFIG.port,
        httpPort: WS_CONFIG.port,
        httpsPort: 443,
        forceTLS: false,
        disableStats: true,
        authEndpoint: '/broadcasting/auth',
        // Required for Laravel WebSockets
        enabledTransports: ['ws', 'wss'],
        // Increase timeout for slower connections
        timeout: 20000,
        // Pong timeout
        pongTimeout: 30000,
      };
      
      if (typeof Pusher !== 'undefined') {
        pusher = new Pusher(WS_CONFIG.appKey, pusherConfig);
      } else if (typeof window.Pusher !== 'undefined') {
        pusher = new window.Pusher(WS_CONFIG.appKey, pusherConfig);
      } else {
        reject(new Error('Pusher library not loaded. Please include pusher-js.'));
        return;
      }

      // Bind to connection events
      pusher.connection.bind('connected', () => {
        console.log('✅ WebSocket connected!');
        resolve();
      });

      pusher.connection.bind('disconnected', () => {
        console.log('❌ WebSocket disconnected');
        channel = null;
      });

      pusher.connection.bind('error', (error) => {
        console.error('❌ WebSocket error:', error);
      });

      // Subscribe to the loans channel
      channel = pusher.subscribe('loans');
      
      // Bind to the loan.created event
      channel.bind('loan.created', (data) => {
        console.log('📢 loan.created event received:', data);
        triggerEvent('loan.created', data);
      });

      // Also bind to pusher internal events for debugging
      channel.bind('pusher:subscription_succeeded', () => {
        console.log('📡 Subscribed to loans channel!');
      });

      channel.bind('pusher:subscription_error', (error) => {
        console.error('❌ Subscription error:', error);
      });
    });
  }

  function disconnect() {
    if (pusher) {
      pusher.disconnect();
      pusher = null;
      channel = null;
    }
  }

  function getChannel() {
    return channel;
  }

  // Export for use in browser
  window.loanWebSocket = {
    connect,
    disconnect,
    on,
    off,
    getChannel,
    // Convenience method for loan.created
    onLoanCreated: (callback) => on('loan.created', callback)
  };

  // Auto-connect on page load for users with roles 1, 6, or 4
  if (typeof document !== 'undefined') {
    document.addEventListener('DOMContentLoaded', function() {
      // Get user role from the page if available
      const userRoleElement = document.querySelector('[data-user-role]');
      const userRole = userRoleElement ? userRoleElement.getAttribute('data-user-role') : null;
      
      // Auto-connect if user has appropriate role
      if (userRole && ['1', '6', '4'].includes(userRole)) {
        console.log('🔌 Auto-connecting to WebSocket for role:', userRole);
        window.loanWebSocket.connect().catch(err => {
          console.error('❌ WebSocket auto-connect failed:', err);
        });
      }
    });
  }
})();
