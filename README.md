# CDS Agenda

This is a dockerized version of the original [CDS
Agenda](https://cds.cern.ch/record/46032) (version 4.2.9).

Openshift site (CERN internal network only):
[http://cdsagenda.app.cern.ch](http://cdsagenda.app.cern.ch).

## Requirements

- Docker
- Netscape Navigator version 4 or later

## Running

Run

```
docker compose up
```

and go to `http://localhost:9090`

## Current status

Most pages load without crashing, but any advanced functionality like
login/registration or anything that requires users being logged in is currently
broken.
