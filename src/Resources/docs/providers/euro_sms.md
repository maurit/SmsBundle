# EuroSMS (APIv3)

Provider to connect with [EuroSMS](https://www.eurosms.com/) service.

## Parameters

 * `id` Integration ID *(required)*
 * `key` Integration Key *(required)*

## Example

``` yaml
# config/maurit_sms.yaml
maurit_sms:
    providers:
        eurosms_provider_doc: # your custom provider name
            euro_sms:
                id: uid
                key: userkey
```
