# SmsGate sk

Provider to connect with [SmsGate](https://www.smsgate.sk/) service.
- [API docs](https://www.smsgate.sk/docs/sms-api-popis/)

## Parameters

* `token` Integration ID *(required)*
* `textNumbers` allow setting `from` parameter *(default false)*
* `unicode` send text as unicode\[UTF16] *(default true)*

## Example

``` yaml
# config/maurit_sms.yaml
maurit_sms:
    providers:
        smsgate_provider_doc: # your custom provider name
            smsgate_sk:
                token: XXXXXXXXXXX
```
