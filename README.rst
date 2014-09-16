########
Ag.Event
########

TYPO3 Flow package to support domain events as described in the Domain Events chapter from: https://vaughnvernon.co/?page_id=168

Why?
====
Because using domain events to communicate information between aggregates, modules and bounded contexts decreases coupling and helps avoiding anemic domain models.

How?
====
See Ag.BlogExample for an example on how to integrate.

****************
Event processing
****************

There is a CLI command which can be run via ``./flow event:process {eventHandlerName}``, where the ``{eventHandlerName}`` is defined in ``Settings.yaml`` for example that way::

  Ag:
    Event:
      eventHandlers:
        async:
          'Acme\Foo\EventHandler\SomeThingTriggeredEventHandler': TRUE

Per convention, the class name's namespace separators are substituted by an undersocre, so the key name in this case would be ``Acme_Foo_EventHandler_SomeThingTriggeredEventHandler``.

Deamonizing configuration
-------------------------

The mentioned CLI command must be kept alive manually because it intrinsically will time out after a specified time range in order to avoid for example the MySQL connection timing out.

In order to achieve that, we have three recommendations here.

Using *supervisord*
^^^^^^^^^^^^^^^^^^^

Have a pool with the following configuration::

    [program:some_thing]
    command=/var/www/production/flow event:process Acme_Foo_EventHandler_SomeThingTriggeredEventHandler
    environment=FLOW_CONTEXT="Production"
    redirect_stderr=true
    stdout_logfile=/var/www/production/Data/Logs/Supervisord.log
    stderr_logfile=/var/www/production/Data/Logs/Supervisord_error.log
    user=theuser
    autostart=true
    autorestart=true
    stopsignal=QUIT

Using *daemon*
^^^^^^^^^^^^^^

Start the process with a command similar to this::

    daemon --name="some_thing" --env="FLOW_CONTEXT=Production" --respawn --stdout="/var/www/production/Data/Logs/DaemonStdOut.log" --stderr="/var/www/production/Data/Logs/DaemonStdErr.log" /var/www/production/flow event:process Acme_Foo_EventHandler_SomeThingTriggeredEventHandler

Once started, you can stop this deamon with ``daemon --stop --name="some_thing"``.

Using *upstart*
^^^^^^^^^^^^^^^

Have an upstart config with especially ``respawn`` active.
