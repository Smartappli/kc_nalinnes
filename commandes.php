<form method="post">
    <h1>Souper du karaté (KC Nalinnes)</h1>
    <h2>Vendredi 30 janvier 2026 à 20h30 (après les passage de grade)</h2>
    <br>
    Lieu: Cafétaria du centre sportif<br>
    <br>
    <strong>Menu (boissons non comprises)</strong>
    <table>
        <tr>
            <th>Description</th>
            <th>Prix</th>
            <th>Quantité</th>
            <th>Total</th>
        </tr>
        <tr>
            <td>Adultes: Un américain + frites</td>
            <td>17</td>
            <td><input type="text" name="repas_adulte" value="0"></td>
            <td><span id="total_repas_adulte">0</span></td>
        </tr>
        <tr>
            <td>Enfants: Une boulette + frites</td>
            <td>10</td>
            <td><input type="text" name="repas_enfant" value="0"></td>
            <td><span id="total_repas_enfant">0</span></td>
        </tr>
    </table>
    <br>
    <strong>Réservation</strong>
    <p><label>Nom : </label><input type="text" name="nom" placeholder="Nom du karatéka"></p>
    <p><label>Prénom : </label><input type="text" name="prenom" placeholder="Prénom du karateka"></p>
    <p><label>Total à payer : </label><span id="Total_a_payer">0</span> €</p>
    <br>
    <input type="submit">
</form>