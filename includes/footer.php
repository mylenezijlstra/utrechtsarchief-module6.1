<style>
    .archief-footer {
        background: white;
        padding: 3rem 6rem;
        font-family: system-ui, sans-serif;
        font-size: 0.95rem;
        color: #333;
    }

    .footer-columns {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        justify-content: space-between;
    }

    .footer-col {
        flex: 1 1 220px;
        min-width: 200px;
    }

    .footer-col h4 {
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
        color: #000;
    }

    .footer-col ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-col ul li {
        margin-bottom: 0.4rem;
    }

    .footer-col a {
        text-decoration: none;
        color: rgba(0, 0, 0, 1);
    }

    .newsletter {
        margin: 1rem 0;
    }

    .newsletter input {
        padding: 0.4rem;
        width: 70%;
        margin-right: 0.5rem;
        border: 1px solid #d9d9d9ff;
        border-radius: 2px;
    }

    .newsletter button {
        padding: 0.4rem 0.8rem;
        background: rgba(215, 47, 25, 1);
        color: white;
        border: none;
        border-radius: 2px;
        cursor: pointer;
    }

    .social-icons {
        font-size: 1.4rem;
        margin: 0.5rem 0;
    }

    .icon {
        width: 49px !important;
        height: auto;
    }

    .contact-item {
        display: flex;
        /* zet afbeelding en tekst naast elkaar */
        align-items: flex-start;
        /* bovenaan uitlijnen */
        gap: 0.8rem;
        /* ruimte tussen foto en tekst */
    }

    .contact-item .icon {
        width: 40px;
        /* maak icoon kleiner */
        height: auto;
    }

    .contact-item p {
        margin-top: -5px;
        /* duwt tekst iets omhoog */
    }

    .archief-footer {
        font-weight: bold;
    }
</style>

<footer class="archief-footer">
    <div class="footer-columns">
        <div class="footer-col">
            <h4>Plan een bezoek</h4>
            <p>› Expo - Hamburgerstraat 28<br>› Studiezaal - Alexander Numankade 199 - 201</p>

            <h4>Onderzoek</h4>
            <ul>
                <li><a href="#">› Archieven doorzoeken</a></li>
                <li><a href="#">› Beeldmateriaal bekijken</a></li>
                <li><a href="#">› Bouwtekeningen</a></li>
                <li><a href="#">› Personen zoeken</a></li>
            </ul><br><br>

            <form class="newsletter">
               <h3> <label for="email">Blijf op de hoogte</label><br></h3>
                <input type="email" id="email" placeholder="E-mailadres">
                <button type="submit">Verstuur</button>
            </form>
        </div>
        <div class="footer-col">
            <h4>Over ons</h4>
            <ul>
                <li><a href="#">› Nieuws</a></li>
                <li><a href="#">› Agenda</a></li>
                <li><a href="#">› Uw materiaal in ons archief</a></li>
                <li><a href="#">› Contact</a></li>
                <li><a href="#">› Toegankelijkheid</a></li>
                <li><a href="#">› Auteursrecht en disclaimer</a></li>
                <li><a href="#">› Privacyverklaring</a></li>
                <li><a href="#">› ANBI</a></li>
                <li><a href="#">› English</a></li>
            </ul>


        </div>


        <div class="footer-col">
            <h4>Contact</h4>
            <div class="contact-item">
                <img src="./assets/img/icoontjes.png" class="icon">
                <p>
                    (030) 286 66 11<br><br>
                    <a href="mailto:inlichtingen@hetutrechtsarchief.nl">inlichtingen@hetutrechtsarchief.nl</a><br><br>
                    Postbus 131, 3500 AC Utrecht<br><br>
                    Chat: di t/m do 9.00 - 13.00 uur
                </p>
            </div>



            <div class="social-icons">
                <img src="./assets/img/volg.png" alt="facebook, instagram youtube en RSS">

            </div>
            <p>IBAN: NL66RABO0123881641<br>
                KvK: 62047302<br>
                BTW: NL807024594B01</p>
        </div>
    </div>
</footer>