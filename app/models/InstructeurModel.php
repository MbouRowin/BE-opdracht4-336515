<?php

class InstructeurModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getInstructeurs()
    {
        $sql = "SELECT Id
                      ,Voornaam
                      ,Tussenvoegsel
                      ,Achternaam
                      ,Mobiel
                      ,DatumInDienst
                      ,AantalSterren
                FROM  Instructeur
                ORDER BY AantalSterren DESC";

        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getTypeVoertuigen()
    {
        $sql = "SELECT Id
                      ,TypeVoertuig
                FROM  TypeVoertuig";

        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getToegewezenVoertuigen($Id)
    {
        $sql = "SELECT       VOER.Type
                            ,VOER.Kenteken
                            ,VOER.Bouwjaar
                            ,VOER.Brandstof
                            ,TYVO.TypeVoertuig
                            ,TYVO.RijbewijsCategorie
                            ,VOER.Id

                FROM        Voertuig    AS  VOER
                
                INNER JOIN  TypeVoertuig AS TYVO

                ON          TYVO.Id = VOER.TypeVoertuigId
                
                INNER JOIN  VoertuigInstructeur AS VOIN
                
                ON          VOIN.VoertuigId = VOER.Id
                
                WHERE       VOIN.InstructeurId = $Id
                
                ORDER BY    TYVO.RijbewijsCategorie DESC";

        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getBeschikbareVoertuigen()
    {
        $sql = "SELECT       VOER.Type
                            ,VOER.Kenteken
                            ,VOER.Bouwjaar
                            ,VOER.Brandstof
                            ,TYVO.TypeVoertuig
                            ,TYVO.RijbewijsCategorie
                            ,VOER.Id

                FROM        Voertuig    AS  VOER
                
                INNER JOIN  TypeVoertuig AS TYVO

                ON          TYVO.Id = VOER.TypeVoertuigId
                
                LEFT JOIN  VoertuigInstructeur AS VOIN
                
                ON          VOIN.VoertuigId = VOER.Id
                
                WHERE       VOIN.InstructeurId is null
                
                ORDER BY    VOER.Bouwjaar DESC";

        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getInstructeurById($Id)
    {
        $sql = "SELECT Voornaam
                      ,Tussenvoegsel
                      ,Achternaam
                      ,DatumInDienst
                      ,AantalSterren
                      ,Id
                FROM  Instructeur
                WHERE Id = $Id";

        $this->db->query($sql);

        return $this->db->single();
    }

    public function getVoertuigById($id)
    {
        $sql = "SELECT Kenteken
        ,Type
        ,Bouwjaar
        ,Brandstof
        ,TypeVoertuigId
                FROM  Voertuig
                WHERE Id = $id";

        $this->db->query($sql);

        return $this->db->single();
    }

    public function getVoertuigInstructeur($id)
    {
        $sql = "SELECT voertuiginstructeur.instructeurid as Id FROM voertuig
        join voertuiginstructeur on voertuiginstructeur.voertuigid = voertuig.id
        where voertuig.id = ?";

        $this->db->query($sql);
        $this->db->bind(1, $id);
        return $this->db->single()->Id;
    }

    function updateVoertuig(
        $id,
        $instructeur,
        $typeVoertuig,
        $type,
        $bouwjaar,
        $brandstof,
        $kenteken
    ) {
        $sql = "update voertuig set type = ?,
        bouwjaar = ?,
        brandstof = ?,
        kenteken = ?,
        typevoertuigid = ?
        where id = ?";

        $this->db->query($sql);
        $this->db->bind(1, $type);
        $this->db->bind(2, $bouwjaar);
        $this->db->bind(3, $brandstof);
        $this->db->bind(4, $kenteken);
        $this->db->bind(5, $typeVoertuig);
        $this->db->bind(6, $id);
        $this->db->single();

        $sql = "update voertuiginstructeur set instructeurid = ? where voertuigid = ?";

        $this->db->query($sql);
        $this->db->bind(1, $instructeur);
        $this->db->bind(2, $id);
        $this->db->single();
    }

    function assignVoertuigToInstructeur($voertuigId, $instructeurId)
    {
        $sql = "insert into voertuiginstructeur (voertuigid, instructeurid, datumtoekenning, isactief, opmerkingen, datumaangemaakt, datumgewijzigd) values (?, ?, ?, 1, NULL, SYSDATE(6), SYSDATE(6))";

        $this->db->query($sql);
        $this->db->bind(1, $voertuigId);
        $this->db->bind(2, $instructeurId);
        $this->db->bind(3, date('Y-m-d'));
        $this->db->single();
    }

    function unassignVoertuig($voertuigId)
    {
        $sql = "delete from voertuiginstructeur where voertuigid = ?";
        $this->db->query($sql);
        $this->db->bind(1, $voertuigId);
        $this->db->single();
    }

    function verwijderVoertuig($voertuigId)
    {
        $sql = "delete from voertuig where id = ?";
        $this->db->query($sql);
        $this->db->bind(1, $voertuigId);
        $this->db->single();
    }

    public function getAlleVoertuigen()
    {
        $sql = "SELECT       VOER.Type
                            ,VOER.Kenteken
                            ,VOER.Bouwjaar
                            ,VOER.Brandstof
                            ,TYVO.TypeVoertuig
                            ,TYVO.RijbewijsCategorie
                            ,VOER.Id
                            ,CONCAT(INS.Voornaam, ' ', INS.Tussenvoegsel, ' ', INS.Achternaam) as InstructeurNaam

                FROM        Voertuig    AS  VOER
                
                INNER JOIN  TypeVoertuig AS TYVO

                ON          TYVO.Id = VOER.TypeVoertuigId
                
                LEFT JOIN  VoertuigInstructeur AS VOIN
                
                ON          VOIN.VoertuigId = VOER.Id

                LEFT JOIN Instructeur AS INS ON VOIN.InstructeurId = INS.Id
                
                ORDER BY    VOER.Bouwjaar DESC";

        $this->db->query($sql);
        return $this->db->resultSet();
    }
}
