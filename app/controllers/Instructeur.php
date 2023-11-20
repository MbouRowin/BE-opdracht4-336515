<?php

class Instructeur extends BaseController
{
    private $instructeurModel;

    public function __construct()
    {
        $this->instructeurModel = $this->model('InstructeurModel');
    }

    public function overzichtInstructeur()
    {
        $result = $this->instructeurModel->getInstructeurs();
        $rows = "";

        foreach ($result as $instructeur) {
            /**
             * Datum in het juiste formaat gezet
             */
            $date = date_create($instructeur->DatumInDienst);
            $formatted_date = date_format($date, 'd-m-Y');

            $actiefInactiefHtml = $instructeur->IsActief
                ? "<a href='/instructeur/maakInactief/$instructeur->Id'><i class='bi bi-hand-thumbs-up'></i></a>"
                : "<a href='/instructeur/maakActief/$instructeur->Id'><i class='bi bi-bandaid'></i></a>";

            $rows .= "<tr>
                        <td>$instructeur->Voornaam</td>
                        <td>$instructeur->Tussenvoegsel</td>
                        <td>$instructeur->Achternaam</td>
                        <td>$instructeur->Mobiel</td>
                        <td>$formatted_date</td>            
                        <td>$instructeur->AantalSterren</td>            
                        <td>
                            <a href='" . URLROOT . "/instructeur/overzichtVoertuigen/$instructeur->Id'>
                                <i class='bi bi-car-front'></i>
                            </a>
                        </td>
                        <td>$actiefInactiefHtml</td>         
                      </tr>";
        }

        $data = [
            'title' => 'Instructeurs in dienst',
            'rows' => $rows
        ];

        $this->view('Instructeur/overzichtInstructeur', $data);
    }

    public function overzichtVoertuigen($Id)
    {
        session_start();
        $message = $_SESSION["message"] ?? null;
        unset($_SESSION["message"]);

        $instructeurInfo = $this->instructeurModel->getInstructeurById($Id);

        // var_dump($instructeurInfo);
        $naam = $instructeurInfo->Voornaam . " " . $instructeurInfo->Tussenvoegsel . " " . $instructeurInfo->Achternaam;
        $datumInDienst = $instructeurInfo->DatumInDienst;
        $aantalSterren = $instructeurInfo->AantalSterren;

        /**
         * We laten de model alle gegevens ophalen uit de database
         */
        $result = $this->instructeurModel->getToegewezenVoertuigen($Id);


        $tableRows = "";
        if (empty($result)) {
            /**
             * Als er geen toegewezen voertuigen zijn komt de onderstaande tekst in de tabel
             */
            $tableRows = "<tr>
                            <td colspan='9'>
                                Er zijn op dit moment nog geen voertuigen toegewezen aan deze instructeur
                            </td>
                          </tr>";
        } else {
            /**
             * Bouw de rows op in een foreach-loop en stop deze in de variabele
             * $tabelRows
             */
            foreach ($result as $voertuig) {

                /**
                 * Zet de datum in het juiste format
                 */
                $date_formatted = date_format(date_create($voertuig->Bouwjaar), 'd-m-Y');

                $toegewezenHtml = $voertuig->Multiple
                    ? "<a href='/instructeur/reassignVoertuig/$instructeurInfo->Id/$voertuig->Id'>❌</a>"
                    : "✅";

                $tableRows .= "<tr>
                                    <td>$voertuig->TypeVoertuig</td>
                                    <td>$voertuig->Type</td>
                                    <td>$voertuig->Kenteken</td>
                                    <td>$date_formatted</td>
                                    <td>$voertuig->Brandstof</td>
                                    <td>$voertuig->RijbewijsCategorie</td>
                                    <td><a href='/instructeur/wijzig/$voertuig->Id'>Wijzigen</a></td>
                                    <td><a href='/instructeur/unassign/$Id/$voertuig->Id'>Verwijderen</a></td>
                                    <td>$toegewezenHtml</td>
                            </tr>";
            }
        }


        $data = [
            'title'     => 'Door instructeur gebruikte voertuigen',
            'tableRows' => $tableRows,
            'naam'      => $naam,
            'datumInDienst' => $datumInDienst,
            'aantalSterren' => $aantalSterren,
            'id' => $Id,
            'message' => $message,
        ];

        $this->view('Instructeur/overzichtVoertuigen', $data);
    }

    function wijzig($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $currentInstructeur = $this->instructeurModel->getVoertuigInstructeur($id);

            $instructeur = $_POST["instructeur"];
            $typeVoertuig = $_POST["type_voertuig"];
            $type = $_POST["type"];
            $bouwjaar = $_POST["bouwjaar"];
            $brandstof = $_POST["brandstof"];
            $kenteken = $_POST["kenteken"];

            $this->instructeurModel->updateVoertuig($id, $instructeur, $typeVoertuig, $type, $bouwjaar, $brandstof, $kenteken);

            if ($currentInstructeur) {
                header("Location: /instructeur/overzichtVoertuigen/$currentInstructeur");
            } else {
                $this->instructeurModel->assignVoertuigToInstructeur($id, $instructeur);
                header("Location: /instructeur/overzichtVoertuigen/$instructeur");
            }
        } else {
            $instructeurs = $this->instructeurModel->getInstructeurs();
            $typeVoertuig = $this->instructeurModel->getTypeVoertuigen();
            $voertuig = $this->instructeurModel->getVoertuigById($id);

            $data = [
                'title'     => 'Wijzigen voertuiggegevens',
                'instructeurs' => $instructeurs,
                'typeVoertuig' => $typeVoertuig,
                'voertuig' => $voertuig,
            ];

            $this->view("Instructeur/wijzigen", $data);
        }
    }

    public function toevoegenOverzicht($Id)
    {
        session_start();
        $message = $_SESSION["message"] ?? null;
        unset($_SESSION["message"]);

        $instructeurInfo = $this->instructeurModel->getInstructeurById($Id);

        // var_dump($instructeurInfo);
        $naam = $instructeurInfo->Voornaam . " " . $instructeurInfo->Tussenvoegsel . " " . $instructeurInfo->Achternaam;
        $datumInDienst = $instructeurInfo->DatumInDienst;
        $aantalSterren = $instructeurInfo->AantalSterren;

        /**
         * We laten de model alle gegevens ophalen uit de database
         */
        $result = $this->instructeurModel->getBeschikbareVoertuigen($Id);


        $tableRows = "";
        if (empty($result)) {
            /**
             * Als er geen toegewezen voertuigen zijn komt de onderstaande tekst in de tabel
             */
            $tableRows = "<tr>
                            <td colspan='9'>
                                Er zijn op dit moment geen beschikbare voertuigen
                            </td>
                          </tr>
                          <script>
                            setTimeout(() => location = '/Instructeur/overzichtInstructeur', 3000);
                          </script>
                          ";
        } else {
            /**
             * Bouw de rows op in een foreach-loop en stop deze in de variabele
             * $tabelRows
             */
            foreach ($result as $voertuig) {

                /**
                 * Zet de datum in het juiste format
                 */
                $date_formatted = date_format(date_create($voertuig->Bouwjaar), 'd-m-Y');

                $tableRows .= "<tr>
                                    <td>$voertuig->TypeVoertuig</td>
                                    <td>$voertuig->Type</td>
                                    <td>$voertuig->Kenteken</td>
                                    <td>$date_formatted</td>
                                    <td>$voertuig->Brandstof</td>
                                    <td>$voertuig->RijbewijsCategorie</td>
                                    <td><a href='/instructeur/toevoegen/$voertuig->Id/$instructeurInfo->Id'>Toevoegen</a></td>
                                    <td><a href='/instructeur/wijzig/$voertuig->Id'>Wijzigen</a></td>
                                    <td><a href='/instructeur/verwijder/$Id/$voertuig->Id'>Verwijderen</a></td>
                            </tr>";
            }
        }


        $data = [
            'title'     => 'Toevoegen voertuig',
            'tableRows' => $tableRows,
            'naam'      => $naam,
            'datumInDienst' => $datumInDienst,
            'aantalSterren' => $aantalSterren,
            'message' => $message,
        ];

        $this->view('Instructeur/toevoegen', $data);
    }

    public function toevoegen($voertuigId, $instructeurId)
    {
        $this->instructeurModel->assignVoertuigToInstructeur($voertuigId, $instructeurId);
        header("Location: /instructeur/overzichtVoertuigen/$instructeurId");
    }

    function unassign($instructeurId, $voertuigId)
    {
        $this->instructeurModel->unassignVoertuig($voertuigId);

        session_start();
        $_SESSION["message"] = "Het door u geselecteerde voertuig is verwijderd";

        header("Location: /instructeur/overzichtVoertuigen/$instructeurId");
    }

    function verwijder($instructeurId, $voertuigId)
    {
        $this->instructeurModel->verwijderVoertuig($voertuigId);

        session_start();
        $_SESSION["message"] = "Het door u geselecteerde voertuig is verwijderd";

        header("Location: /instructeur/toevoegenOverzicht/$instructeurId");
    }

    public function overzichtAlleVoertuigen()
    {
        session_start();
        $message = $_SESSION["message"] ?? null;
        unset($_SESSION["message"]);

        $result = $this->instructeurModel->getAlleVoertuigen();


        $tableRows = "";
        if (empty($result)) {
            /**
             * Als er geen toegewezen voertuigen zijn komt de onderstaande tekst in de tabel
             */
            $tableRows = "<tr>
                            <td colspan='9'>
                                Er zijn op dit moment geen voertuigen
                            </td>
                          </tr>
                          ";
        } else {
            /**
             * Bouw de rows op in een foreach-loop en stop deze in de variabele
             * $tabelRows
             */
            foreach ($result as $voertuig) {

                /**
                 * Zet de datum in het juiste format
                 */
                $date_formatted = date_format(date_create($voertuig->Bouwjaar), 'd-m-Y');

                $tableRows .= "<tr>
                                    <td>$voertuig->TypeVoertuig</td>
                                    <td>$voertuig->Type</td>
                                    <td>$voertuig->Kenteken</td>
                                    <td>$date_formatted</td>
                                    <td>$voertuig->Brandstof</td>
                                    <td>$voertuig->RijbewijsCategorie</td>
                                    <td>$voertuig->InstructeurNaam</td>
                                    <td><a href='/instructeur/wijzig/$voertuig->Id'>Wijzigen</a></td>
                                    <td><a href='/instructeur/unassignEnVerwijder/$voertuig->Id'>Verwijderen</a></td>
                            </tr>";
            }
        }


        $data = [
            'title'     => 'Alle voertuigen',
            'tableRows' => $tableRows,
            'message' => $message,
        ];

        $this->view('Instructeur/overzichtAlleVoertuigen', $data);
    }

    public function overzichtBeschikbareVoertuigen()
    {
        session_start();
        $message = $_SESSION["message"] ?? null;
        unset($_SESSION["message"]);

        $result = $this->instructeurModel->getBeschikbareVoertuigen();


        $tableRows = "";
        if (empty($result)) {
            /**
             * Als er geen toegewezen voertuigen zijn komt de onderstaande tekst in de tabel
             */
            $tableRows = "<tr>
                            <td colspan='9'>
                                Er zijn op dit moment geen voertuigen
                            </td>
                          </tr>
                          ";
        } else {
            /**
             * Bouw de rows op in een foreach-loop en stop deze in de variabele
             * $tabelRows
             */
            foreach ($result as $voertuig) {

                /**
                 * Zet de datum in het juiste format
                 */
                $date_formatted = date_format(date_create($voertuig->Bouwjaar), 'd-m-Y');

                $tableRows .= "<tr>
                                    <td>$voertuig->TypeVoertuig</td>
                                    <td>$voertuig->Type</td>
                                    <td>$voertuig->Kenteken</td>
                                    <td>$date_formatted</td>
                                    <td>$voertuig->Brandstof</td>
                                    <td>$voertuig->RijbewijsCategorie</td>
                            </tr>";
            }
        }


        $data = [
            'title'     => 'Alle beschikbare voertuigen',
            'tableRows' => $tableRows,
            'message' => $message,
        ];

        $this->view('Instructeur/overzichtBeschikbareVoertuigen', $data);
    }

    function unassignEnVerwijder($voertuigId)
    {
        $this->instructeurModel->unassignVoertuig($voertuigId);
        $this->instructeurModel->verwijderVoertuig($voertuigId);

        session_start();
        $_SESSION["message"] = "Het door u geselecteerde voertuig is verwijderd";

        header("Location: /instructeur/overzichtAlleVoertuigen");
    }

    function maakActief($instructeurId)
    {
        $instructeur = $this->instructeurModel->getInstructeurById($instructeurId);
        $this->instructeurModel->maakActief($instructeurId);

        session_start();
        $_SESSION["message"] = "Instructeur $instructeur->Voornaam $instructeur->Tussenvoegsel $instructeur->Achternaam is beter/terug van verlof gemeld";

        header("Location: /instructeur/overzichtVoertuigen/$instructeurId");
    }

    function maakInactief($instructeurId)
    {
        $instructeur = $this->instructeurModel->getInstructeurById($instructeurId);
        $this->instructeurModel->maakInactief($instructeurId);

        session_start();
        $_SESSION["message"] = "Instructeur $instructeur->Voornaam $instructeur->Tussenvoegsel $instructeur->Achternaam is ziek/met verlof gemeld";

        header("Location: /instructeur/overzichtVoertuigen/$instructeurId");
    }

    function reassignVoertuig($instructeurId, $voertuigId)
    {
        $this->instructeurModel->unassignVoertuig($voertuigId);
        $this->instructeurModel->assignVoertuigToInstructeur($voertuigId, $instructeurId);

        session_start();
        $_SESSION["message"] = "Het geselecteerde voertuig is weer toegewezen";

        header("Location: /instructeur/overzichtVoertuigen/$instructeurId");
    }
}
