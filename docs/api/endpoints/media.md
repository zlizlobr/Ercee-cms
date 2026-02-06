# Media

## GET /api/v1/media
List media items.

### Authorization
Public endpoint. No authentication required.

### Query parameters
None.

### Successful response
Returns a list of media objects.

### Error responses
Standard error response format.

## GET /api/v1/media/{uuid}
Fetch a single media item by UUID.

### Authorization
Public endpoint. No authentication required.

### Path parameters
- `uuid` (string) - Media UUID.

### Successful response
Returns a single media object.

### Error responses
Standard error response format.

## POST /api/v1/media/resolve
Resolve media references from the request body.

### Authorization
Public endpoint. No authentication required.

### Request body
Payload depends on the client usage; typically contains media identifiers to resolve.

### Successful response
Returns resolved media objects.

### Error responses
Standard error response format.
