<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colofonpagina</title>

    <style>
        :root {
            /* Utrechts Archief geïnspireerde stijl */
            --ua-red: #C4002E;
            /* krachtig Utrecht rood */
            --ua-ink: #1A1A1A;
            /* hoofdtekst */
            --ua-muted: #5A5A5A;
            /* secundair */
            --ua-bg: #FAF8F6;
            /* zacht, licht achtergrond */
            --ua-card: #FFFFFF;
            /* paneelkleur */
            --radius: 12px;
            --shadow: 0 1px 2px rgba(0, 0, 0, 0.06), 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        /* Basistypografie en layout */
        html {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Inter, Arial, sans-serif;
        }

        body {
            margin: 0;
            background: linear-gradient(180deg, var(--ua-bg), #fff 40%);
            color: var(--ua-ink);
        }

        .page {
            max-width: 880px;
            margin: 4rem auto;
            padding: 0 1.25rem;
        }

        .card {
            background: var(--ua-card);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        /* Bovenste merkbalk */
        .brandbar {
            height: 10px;
            background: var(--ua-red);
        }

        /* Titelblok */
        .header {
            padding: 2rem 2rem 1.5rem;
            border-bottom: 1px solid #eee;
        }

        .kicker {
            display: inline-block;
            font-size: 0.78rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--ua-muted);
            margin-bottom: .5rem;
        }

        h1 {
            margin: 0;
            font-size: clamp(1.8rem, 3.5vw, 2.4rem);
            line-height: 1.15;
        }

        /* Inhoud */
        .content {
            padding: 1.5rem 2rem 2rem;
            display: grid;
            gap: 1.75rem;
        }

        section {
            position: relative;
            padding-left: 1rem;
        }

        section::before {
            content: "";
            position: absolute;
            left: 0;
            top: .35rem;
            bottom: .35rem;
            width: 3px;
            background: var(--ua-red);
            border-radius: 3px;
            opacity: .9;
        }

        h2 {
            font-size: clamp(1.15rem, 2.4vw, 1.35rem);
            margin: 0 0 .5rem;
        }

        p {
            margin: 0 0 .75rem;
            line-height: 1.6;
            color: var(--ua-ink);
        }

        /* Definition list voor credits */
        .credits {
            display: grid;
            gap: .5rem;
        }

        .credits dt {
            font-weight: 600;
            color: var(--ua-ink);
        }

        .credits dd {
            margin: 0 0 .5rem 0;
            color: var(--ua-muted);
        }

        /* Toegankelijkheid: grotere targets en goede contrasten */
        a {
            color: var(--ua-red);
            text-underline-offset: 2px;
            text-decoration-thickness: 1.5px;
        }

        /* Responsief fine-tuning */
        @media (min-width: 680px) {
            .content {
                grid-template-columns: 1fr;
            }
        }

        /* Printvriendelijk */
        @media print {
            body {
                background: #fff;
            }

            .card {
                box-shadow: none;
                border: 1px solid #ddd;
            }

            .brandbar {
                height: 6px;
            }
        }
    </style>
</head>

<body>

    <header>
        <?php
        include "includes/header.php"
        ?>
    </header>

    <main class="page" aria-labelledby="colofon-title">
        <article class="card">
            <div class="brandbar" aria-hidden="true"></div>

            <div class="content">
                <section aria-labelledby="inleiding">
                    <h2 id="inleiding">Colofon</h2>
                    <p>Deze website is tot stand gekomen als onderdeel van een educatief en creatief ontwikkeltraject, uitgevoerd door Mylène Zijlstra en Iris Govaard. Met veel zorg, aandacht en enthousiasme hebben wij gewerkt aan het ontwerp, de vormgeving en de inhoudelijke opbouw van deze website. Ons doel was om een toegankelijke, overzichtelijke en informatieve online omgeving te creëren waarin het erfgoed van Utrecht op een boeiende manier wordt gepresenteerd.</p>
                </section>

                <section aria-labelledby="opdrachtgever">
                    <h2 id="opdrachtgever">Opdrachtgever</h2>
                    <p>Dit project is ontwikkeld in opdracht van Het Utrechts Archief, de bewaarplaats van de rijke geschiedenis van de stad en provincie Utrecht. Door deze samenwerking kregen wij de kans om diep in het historische materiaal te duiken en dit te vertalen naar een digitaal eindresultaat dat aansluit bij de missie van het archief: het delen, bewaren en toegankelijk maken van cultureel erfgoed.</p>
                </section>

                <section aria-labelledby="contactpersoon">
                    <h2 id="contactpersoon">Contactpersoon</h2>
                    <p>Gedurende het gehele traject hadden wij nauw contact met Ellen Blom, die vanuit Het Utrechts Archief de rol van begeleider en aanspreekpunt vervulde. Haar feedback, deskundigheid en betrokkenheid waren van onschatbare waarde voor de ontwikkeling van de website. Dankzij haar begeleiding kon het project groeien van een concept naar een volledig functionele en inhoudelijk sterke website.</p>
                </section>

                <section aria-labelledby="onderwijsinstelling">
                    <h2 id="onderwijsinstelling">Onderwijsinstelling</h2>
                    <p>Dit project is mede mogelijk gemaakt door het Grafisch Lyceum Utrecht. De school bood de ruimte, begeleiding en faciliteiten die nodig waren om dit werk te realiseren. Binnen onze opleiding kregen wij de kans om onze creatieve en technische vaardigheden verder te ontwikkelen en in de praktijk te brengen, met dit eindproduct als resultaat.</p>
                </section>

                <section aria-labelledby="ontwerp-realisatie">
                    <h2 id="ontwerp-realisatie">Ontwerp &amp; Realisatie</h2>
                    <dl class="credits">
                        <dt>Conceptontwikkeling</dt>
                        <dd>Iris Govaard &amp; Mylène Zijlstra</dd>

                        <dt>Vormgeving &amp; UX-design</dt>
                        <dd> Iris Govaard &amp; Mylène Zijlstra</dd>

                        <dt>Tekst &amp; Contentselectie</dt>
                        <dd>In samenwerking met Het Utrechts Archief</dd>

                        <dt>Technische uitwerking &amp; Websitebouw</dt>
                        <dd> Iris Govaard &amp; Mylène Zijlstra</dd>
                    </dl>
                    <p>Wij hebben gebruikgemaakt van diverse digitale hulpmiddelen om het ontwerp en de bouw van de website zo efficiënt en zorgvuldig mogelijk te realiseren. Daarbij lag de nadruk op toegankelijkheid, consistentie in vormgeving en een gebruikservaring die past bij een breed publiek.</p>
                </section>

                <section aria-labelledby="dankwoord">
                    <h2 id="dankwoord">Dankwoord</h2>
                    <p>Wij willen graag Het Utrechts Archief, en in het bijzonder Ellen Blom, bedanken voor hun vertrouwen, betrokkenheid en ondersteuning. Ook spreken wij onze dank uit aan het Grafisch Lyceum Utrecht voor het bieden van de mogelijkheden om dit project te ontwikkelen binnen onze opleiding. Tot slot bedanken wij iedereen die op enige wijze heeft bijgedragen aan de totstandkoming van deze website.</p>
                </section>
            </div>
        </article>
    </main>

    <footer>
        <?php
        include "includes/footer.php"
        ?>
    </footer>


</html>