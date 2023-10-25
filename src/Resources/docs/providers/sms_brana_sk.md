# SmsBrana sk

Provider to connect with [Sms-Brana sk](https://www.sms-brana.sk/) service.

## Parameters

 * `login` User login *(required)*
 * `password` User password *(required)*

## Example

``` yaml
# config/maurit_sms.yaml
maurit_sms:
    providers:
        smsbranask_provider_doc: # your custom provider name
            sms_brana_sk:
                login: user
                password: upswd
```