# LankaQR generator
We can use this codebase to generate LankaQR compatible QR codes without using any API, Website, or App.
Data needed for QR codes can be extracted easily from decoding a LankaQR QR code. 
LankaQR is using the EMV® QR Code Specification (www.emvco.com) for further information we can use their documentation.

# QR code decoding
The data contained within a QR Code is organized as follows. Each data object is made up of three individual fields. The first field is an identifier (ID) by which the data object can be referenced. The next field is a length field that explicitly indicates the number of characters included in the third field: the value field. A data object is then represented as an ID / Length / Value combination, where:
* The ID is coded as a two-digit numeric value, with a value ranging from "00" to "99",
* The length is coded as a two-digit numeric value, with a value ranging from "01" to"99",
*  The value field has a minimum length of one character and a maximum length of 99 characters.

Following is a payload contained in a LankaQR QR code. This code was generated from the Upay app.
```0002010102122632002816728000729900000000810600025204729953031445406200.005802LK5916Nuwan Rathnayake6007Colombo610500800625800329dc315db4a4147019ef45ed58040612405181630781699849-000263044939```

## Now let's decrypt it

```0002 01``` 
This part defines the QR code template version. As far as my knowledge this part is static for all codes.

```0102 12``` 
This part defines transaction type whether this code is used for multiple transactions or not ```01``` is the ID and ```02``` is the content length ```11``` is for static payments and  ```12``` is for dynamic payments

```2632 00281672800072990000000081060002``` 
This is the most important part which contains the __Merchant id__,  most of the LankaQR merchant ids contain `32` characters. We need to get this by decoding the QR code. 
 
`5204 7299` 
This part is for merchant category id this is all so from the decoded QR code.

`5303 144` 
This is the currency used in the transaction `144` is for __LKR__ this part is static for LankaQR no need to change this.

`5406 200.00` 
This part is optional if not provided app will prompt the user for the amount. Content length needs to be calculated according to the amount including 2 decimal places. `54` is the ID used in the template for transaction amount. content-length is calculated and added in `XX` format, Here `200.00` contains six characters so it is set to `06`.

`5802 LK` 
This part is for the country code. In LankaQR this part is all so static.

`5916 Nuwan Rathnayake` 
This part is for the merchant name, We have to calculate the content length and add it after the ID.

`6007 Colombo` 
This is the merchant location here also we need to calculate the length and append it.

`6105 00800` 
This is the merchant's postal code. This part is optional.

`6258 00329dc315db4a4147019ef45ed5804061240518 1630781699849-0002` 
This part depends on the merchant registered app. currently I haven't included this part in this code.

`6304 4939` 
Finally, this is the CRC value for checksum. this is calculated using all data objects, including their ID, Length, and Value, to be included in the QR Code, in their respective order, as well as the ID and Length of the CRC itself (but excluding its Value). 

# References
LankaQR specifications: 
https://www.cbsl.gov.lk/sites/default/files/cbslweb_documents/laws/cdg/Payment_and_settlement_systems_circular_no_02_of_2019_e.pdf

EMV® QR Code Specification: 
https://www.emvco.com/wp-content/plugins/pmpro-customizations/oy-getfile.php?u=wp-content/uploads/documents/EMVCo-Merchant-Presented-QR-Specification-v1-1.pdf

CRC calculation: 
https://stackoverflow.com/a/58927628/12334361
