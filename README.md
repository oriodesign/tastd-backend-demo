Tastd Backend Demo
========================

These are just sample files to show coding skills for interviews. 

Many folders and files are missing.



## Changelog

## 1.0.0 (Client 1.7.0)

- [CACHE] New cache manager
- [REVIEW] New filter wishedBy and reviewedBy.
- [FLAG] Search flags based on restaurant averageCost

## 0.9.5 (Client 1.6.2)

- [RESTAURANT] Add distance limit as parameter
- [NOTIFICATION] Update pushed users and filter notifications every 6 hours
- [SCORE] Add reviews count for geoname score
- [CACHE] Invalidate wall reviews when someone upload a picture for those reviews
- [RECAP] Recap Email with csv
- [FACEBOOK] Handle facebook friends pagination
- [NOTIFICATION] Fix facebook notification
- [PUBLIC] Add public api endpoints
- [USER] Featured field with migrations and admin
- [GEONAME] Add featured Geonames
- [POST] Add post endpoint, entity, admin, validator, repository
- [ADMIN] Photo Admin
- [ADMIN] New Marketing Role
- [ADMIN] Use Simple pager
- [CACHE] Invalidate wishes cache when create review

## 0.9.4 (Client 1.6.0)

- [RECAP] New recap email with new style
- [QUEUE] Serialize null for queue
- [CONFIG] Different sqs queue for test

## 0.9.3 (Client 1.5.0)

- [NOTIFICATION] Add notification and push message entity
- [DEVICE] Add device entity
- [APN] Integration with Apple push notification
- [SQS] Integration with Simple Queue Service
- [EMAIL] Recap email

### 0.9.2

- [TAG] Fix tag query
- [CUISINE] Add default cuisine


### 0.9.1 (Client 1.4.0)

- [CUISINE] Add withWish parameter for cuisine filters
- [RESTAURANT] Add orderBy distance
- [SCORE] New Score calculator
- [WISH] Deduce missing fields
- [FLAG] Mix reviews and flags
- [GEONAME] Remove population limit
- [FRIENDS] Add order by lastname to facebook friends

### 0.9.0

- [PHOTO] Add photo gallery
- [SCORE] New score system with geoScore and command
- [OPTION] New option entity
- [TAG] New tag system
- [VARNISH] Add cache manager and headers for varnish
- [FIX] Fix multiple trigger of serializer listener on the same entity

### 0.8.6

- [COMMAND] Add restaurant command
- [RESTAURANT] orderBy=score and cuisine=not_null
- [GEONAME] Get experienced by
- [REVIEW] New filters
- [EXPERTISE] New count property
- [SECURITY] Check permission for restaurant edit
- [BATCH] Add batch methods for review and followers

### 0.8.5

- [MAIL] Change invite email title
- [GEONAME] Reorder by population if there is a large metropolis.
- [GEOCODE] Add test method for reverse geocode
- [GEONAME] Reverse geocoding with fallback with distance
- [INVITE] Fix link with https url


### 0.8.4

- Change missing auth token exception to 401
- Remove elasticsearch
- Remove client url and deep link support
- Register who is invited by who

### 0.8.3

- Remove montserrat from email
- Credential refactoring with permissions
- Remove user.city and add user.geoname
- New random user generator

### 0.8.2

- Color generator command
- Fix image import header
- Add leadersOf parameter
- Add geoname parameter for leaders
- Swap formatted address

### 0.8.1

- Fix user score
- New template for email
- Welcome email
- Change mapping status for message status
- Fix cuisine validation and __toString Geo Translation
- Add get all flags with new parameter leadersOf=1
- Delete wish when insert review

### 0.8.0

- First beta release