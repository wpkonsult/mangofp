<?php

namespace MangoFp\Entities;

class Template extends BaseEntity {
    public function __construct($data = []) {
        parent::__construct();
        $this->data = \array_merge($this->data, [
            'code' => $data['code'] ?? '',
            'modify_time' => $data['modify_time'] ?? '',
			'addresses' => $data['addresses'] ?? [],
			'mainAddresses' => $data['mainAddresses'] ?? [],
			'template' => $data['template'] ?? ''
        ]);
        $this->className = 'Template';
        $this->objectProperties = ['addresses', 'mainAddresses'];
    }

	public static function getDefaultTemplates() {
        return [
            'REGISTERED' => [
                'mainAddresses' => ['[contactEmail]'],
                'addresses' => ['wp@nort.ee'],
                'template' => 'Tere!\n\nSuur tänu! Olete koolitusele registreeritud.\nTäpsema info ja arve saadame enne koolituse algust e-mailile.\n\nTervitustega\nSirli Järviste\n_______________\nN.O.R.T Koolitus\nVaksali 17a, (407), Tartu\nhttps://www.nort.ee\ninfo@nort.ee\ntel. 7428000',
            ],
            'WAIT4CONF' => [
                'mainAddresses' => ['[contactEmail]'],
                'addresses' => ['wp@nort.ee'],
                'template' => 'Tere!\n\nSuur tänu! Olete koolitusele registreeritud.\n\nSaadan Töötukassasse ära registreerimisteate ja annan teada kui neilt kinnitus saabub.\n\nTervitustega\nSirli Järviste\n_______________\nN.O.R.T Koolitus\nVaksali 17a, (407), Tartu\nhttps://www.nort.ee\ninfo@nort.ee\ntel. 7428000',
            ],
            'CONFRECEIVED' => [
                'mainAddresses' => ['[contactEmail]'],
                'addresses' => ['wp@nort.ee'],
                'template' => 'Tere!\n\nTöötukassalt saabus kinnitus, sellega on nüüd kõik korras ja jääb ainult koolitust oodata.\n\nSaadan enne koolituse algust veel täpsustava infomeili.\n\nTervitades\nSirli Järviste\n_______________\nN.O.R.T Koolitus\nVaksali 17a, (407), Tartu\nhttps://www.nort.ee\ninfo@nort.ee\ntel. 7428000',
            ],
            'WAIT4ACCEPT' => [
                'mainAddresses' => ['[contactEmail]'],
                'addresses' => ['wp@nort.ee'],
                'template' => 'Tere!\n\nSuur tänu, et tunnete huvi meie koolituse vastu. Kuna järgmine koolitusaeg ei ole hetkel veel paigas, siis jätame Teid ootelehele ja anname teada kui koolitusaeg selgub.\n\nTervitustega\nSirli Järviste\n_______________\nN.O.R.T Koolitus\nVaksali 17a, (407), Tartu\nhttps://www.nort.ee\ninfo@nort.ee\ntel. 7428000',
            ],
            'WAIT4NEW' => [
                'mainAddresses' => ['[contactEmail]'],
                'addresses' => ['wp@nort.ee'],
                'template' => 'Tere!\n\nSuur tänu, et tunnete huvi meie koolituse vastu. Kuna järgmine koolitusaeg ei ole hetkel veel paigas, siis jätame Teid ootelehele ja anname teada kui koolitusaeg selgub.\n\nTervitustega\nSirli Järviste\n_______________\nN.O.R.T Koolitus\nVaksali 17a, (407), Tartu\nhttps://www.nort.ee\ninfo@nort.ee\ntel. 7428000',
            ],
            'NOTIFIED' => [
                'mainAddresses' => ['[contactEmail]'],
                'addresses' => ['wp@nort.ee'],
                'template' => 'Tere!\n\nOotame Teid esmaspäeval, 06. novembril kell 10.00, Exceli täiendkoolituse esimesele päevale.\n\nKoolitus toimub NORT Koolituse arvutiklassis, Vaksali 17a, ruum 407, Tartu (sissepääs Vaksali tänavalt, lillepoe ja kohvikuga samast uksest, liftiga 4.korrusele, asume otse lifti vastas.)\n\nPanin kaasa ka koolitusarve. Kui midagi oleks selles vaja muuta, siis andke palun teada.\n\nParkimine -  tänava ääres kellaga 90 min tasuta ja alates kella 18.00-st tasuta. Raudtee äärses parklas ja Tiigi tn äärses parklas on kogu aeg tasuta. Lähim linnaliini peatus on „Vaksali“.\n_______________\nN.O.R.T Koolitus\nVaksali 17a, (407), Tartu\nhttps://www.nort.ee\ninfo@nort.ee\ntel. 7428000',
            ],
            'FBASKED' => [
                'mainAddresses' => ['[contactEmail]'],
                'addresses' => ['wp@nort.ee'],
                'template' => 'Tere!\n\nSuur tänu koolitusel "<<label>>" osalemise eest.\nPaluksin nüüd Teilt sellele väikest tagasisidet. Mis hästi, mis halvasti, kas koolitusest oli reaalset kasu jms.\n\nTagasiside  küsimustiku leiate aadressilt https://nort.ee\n\nTervitustega\nSirli Järviste\n_______________\nN.O.R.T Koolitus\nVaksali 17a, (407), Tartu\nhttps://www.nort.ee\ninfo@nort.ee\ntel. 7428000',
            ],
            'NEWSLETTER' => [
                'mainAddresses' => ['[contactEmail]'],
                'addresses' => ['wp@nort.ee'],
                'template' => 'Tere!\n\nAitäh, et liitusite meie uudiskirjaga!.\n\nTänutäheks pakume Teile koolitusel osalemiseks soodustust -10%. (Soodustuse saamiseks, kirjutage koolitusele registreerumisel lisainfo lahtrisse sõna - "uudiskiri") Meie koolituskalendri leiate aadressilt https://nort.ee/koolituskalender/ \n\nKui on küsimusi, siis vastan meeleldi.\n\nTervitustega\nSirli Järviste\n_______________\nN.O.R.T Koolitus\nVaksali 17a, (407), Tartu\nhttps://www.nort.ee\ninfo@nort.ee\ntel. 7428000',
            ],
        ];
	}
}
