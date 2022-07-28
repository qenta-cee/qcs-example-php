<?php
/**
 * QMore Checkout Seamless Demo
 * - Terms of use can be found under
 * https://guides.qenta.com/prerequisites
 * - License can be found under:
 * https://github.com/qenta-cee/qcs-example-php/blob/master/LICENSE.
 */

require_once '../includes/function.inc.php';

// loads the merchant specific parameters from the config file
require_once '../includes/config.inc.php';

session_start();
$_SESSION['amount'] = 30;
$_SESSION['currency'] = 'EUR';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>QMore Checkout Seamless Example</title>
    <link rel="stylesheet" type="text/css" href="style/styles.css">
    <link rel="stylesheet" type="text/css" href="style/q.css">
    <link rel="stylesheet" href="https://use.typekit.net/ucf2gvc.css">
</head>
<body id="bodyStart">
<div id="contentStart">
<form name="form" action="init.php" method="post">
    <table class="csTable" border="1" bordercolor="lightgray" cellpadding="10" cellspacing="0">
        <tr>
            <td colspan="2"><b>Your order details for this demo payment</b></td>
        </tr>
        <tr>
            <td align="right"><b>Amount:</b></td>
            <td><?php echo $_SESSION['currency'].' '.$_SESSION['amount']; ?></td>
        </tr>
        <tr>
            <td align="right"><b>Order ident:</b></td>
            <td><?php echo $_SESSION['orderIdent']; ?></td>
        </tr>
        <tr>
            <td align="right"><b>Payment type:</b></td>
            <td>
                <select name="paymentType" onchange="toggleFinancialInstitutions(this)">
                    <option value="BANCONTACT_MISTERCASH">Bancontact/MisterCash</option>
                    <option value="CCARD" selected>Credit Card</option>
                    <option value="CCARD-MOTO">Credit Card - Mail or Telephone Order</option>
                    <option value="EKONTO">eKonto</option>
                    <option value="SEPA-DD">SEPA Direct Debit</option>
                    <option value="EPS">eps-Überweisung</option>
                    <option value="GIROPAY">giropay</option>
                    <option value="IDL">iDEAL</option>
                    <option value="INSTALLMENT">Installment</option>
                    <option value="INVOICE">Invoice</option>
                    <option value="MAESTRO">Maestro SecureCode</option>
                    <option value="MONETA">Moneta.ru</option>
                    <option value="PRZELEWY24">Przelewy24</option>
                    <option value="PAYPAL">PayPal</option>
                    <option value="PBX">Paybox</option>
                    <option value="POLI">POLi</option>
                    <option value="PSC">Paysafecard</option>
                    <option value="QUICK">Quick</option>
                    <option value="SKRILLWALLET">Skrill Digital Wallet</option>
                    <option value="SOFORTUEBERWEISUNG">sofortueberweisung</option>
                    <option value="TRUSTLY">Trustly</option>
                    <option value="AFTERPAY">Afterpay</option>
                    <option value="VOUCHER">Voucher</option>
                    <option value="CRYPTO">Crypto</option>
                    <option value='CVSPHARMACY'>CVSPHARMACY</option>
                    <option value='DOLLARGENERAL'>DOLLARGENERAL</option>
                    <option value='CIRCLEK'>CIRCLEK</option>
                    <option value='OPENBUCKSCARD'>OPENBUCKSCARD</option>
                    <option value='PAGOEFECTIVO'>PAGOEFECTIVO</option>
                    <option value='SAFETYPAY'>SAFETYPAY</option>
                </select>
            </td>
        </tr>
        <tr id="financialInstitutionIDL" style="display: none;">
            <td align="right"><b>Financial institution:</b></td>
            <td>
                <select name="financialInstitutionIDL">
                    <option value="ABNAMROBANK">ABN Amro Bank</option>
                    <option value="ASNBANK">ASN Bank</option>
                    <option value="BUNQ">Bunq Bank</option>
                    <option value="INGBANK">ING</option>
                    <option value="KNAB">knab</option>
                    <option value="RABOBANK">Rabobank</option>
                    <option value="SNSBANK">SNS Bank</option>
                    <option value="REGIOBANK">RegioBank</option>
                    <option value="TRIODOSBANK">Triodos Bank</option>
                    <option value="VANLANSCHOT">Van Lanschot Bankiers</option>
                </select>
            </td>
        </tr>
        <tr id="financialInstitutionEPS" style="display: none;">
            <td align="right"><b>Financial institution:</b></td>
            <td>
                <select name="financialInstitutionEPS">
                    <option value="ARZ|AB">Apothekerbank</option>
					<option value="ARZ|AAB">Austrian Anadi Bank AG</option>
					<option value="ARZ|BAF">Ärztebank</option>
					<option value="BA-CA">Bank Austria</option>
					<option value="ARZ|BCS">Bankhaus Carl Spängler &amp; Co. AG</option>
					<option value="ARZ|BSS">Bankhaus Schelhammer &amp; Schattera AG</option>
					<option value="Bawag|B">BAWAG P.S.K. AG</option>
					<option value="ARZ|BKS">BKS Bank AG</option>
					<option value="ARZ|BKB">Brüll Kallmus Bank AG</option>
					<option value="ARZ|BTV">BTV VIER LÄNDER BANK</option>
					<option value="ARZ|CBGG">Capital Bank Grawe Gruppe AG</option>
					<option value="ARZ|DB">Dolomitenbank</option>
					<option value="Bawag|E">Easybank AG</option>
					<option value="Spardat|EBS">Erste Bank und Sparkassen</option>
					<option value="ARZ|HAA">Hypo Alpe-Adria-Bank International AG</option>
					<option value="ARZ|VLH">Hypo Landesbank Vorarlberg</option>
					<option value="ARZ|HI">HYPO NOE Gruppe Bank AG</option>
					<option value="ARZ|NLH">HYPO NOE Landesbank AG</option>
					<option value="Hypo-Racon|O">Hypo Oberösterreich</option>
					<option value="Hypo-Racon|S">Hypo Salzburg</option>
					<option value="Hypo-Racon|St">Hypo Steiermark</option>
					<option value="ARZ|HTB">Hypo Tirol Bank AG</option>
					<option value="BB-Racon">HYPO-BANK BURGENLAND Aktiengesellschaft</option>
					<option value="ARZ|IB">Immo-Bank</option>
					<option value="ARZ|OB">Oberbank AG</option>
					<option value="Racon">Raiffeisen Bankengruppe Österreich</option>
					<option value="ARZ|SB">Schoellerbank AG</option>
					<option value="Bawag|S">Sparda Bank Wien</option>
					<option value="ARZ|SBA">Sparda-Bank Austria</option>
					<option value="ARZ|VB">Volksbank Gruppe</option>
					<option value="ARZ|VKB">Volkskreditbank AG</option>
					<option value="ARZ|VRB">VR-Bank Braunau</option>
                </select>
            </td>
        </tr>
        <tr id="financialInstitutionAFTERPAY" style="display: none;">
            <td align="right"><b>Financial institution:</b></td>
            <td>
                <select name="financialInstitutionAFTERPAY">
                    <option value="AFTERPAY-INST">Installment</option>
                    <option value="AFTERPAY-INV">Invoice</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="right"><input type="submit" value="Checkout"/></td>
        </tr>
    </table>
</form>
<script type="text/javascript">
function toggleFinancialInstitutions(select) {
    document.getElementById('financialInstitutionIDL').style.display = 'none';
    document.getElementById('financialInstitutionEPS').style.display = 'none';
    document.getElementById('financialInstitutionAFTERPAY').style.display = 'none';
    var paymentType = select.options[select.selectedIndex].value;
    if(paymentType == "IDL" || paymentType == "EPS" || paymentType == "AFTERPAY") {
        document.getElementById('financialInstitution' + paymentType).style.display = 'table-row';
    }
}
</script>
</div>
</body>
</html>
