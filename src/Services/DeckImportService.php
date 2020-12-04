<?php

namespace App\Services;

use App\Entity\Card;
use App\Entity\Faction;
use App\Entity\Pack;
use App\Entity\PackInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Description of DeckImportService
 *
 * @author cedric
 */
class DeckImportService
{
    public EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $text
     * @param string $delimiter
     * @return array
     */
    public function parseTextImport(string $text, string $delimiter = '==='): array
    {
        $rhett = [
            'decks' => [],
            'errors' => [],
        ];

        $text = trim($text);
        if ('' === $text) {
            return $rhett;
        }

        // load all packs upfront and map them by their names and codes for easy lookup below
        $packs = $this->em->getRepository(Pack::class)->findAll();
        $packsByName = array_combine(array_map(function (PackInterface $pack) {
            return $pack->getName();
        }, $packs), $packs);
        $packsByCode = array_combine(array_map(function (PackInterface $pack) {
            return $pack->getCode();
        }, $packs), $packs);

        $textChunks = explode($delimiter, $text);

        foreach ($textChunks as $text) {
            try {
                $rhett['decks'][] = $this->parseOneTextImport($text, $packsByName, $packsByCode);
            } catch (Exception $e) {
                $rhett['errors'][] = $e->getMessage();
            }
        }

        return $rhett;
    }

    /**
     * @param string $text
     * @param array $packsByName
     * @param array $packsByCode
     * @return array
     * @throws Exception
     */
    protected function parseOneTextImport(string $text, array $packsByName, array $packsByCode): array
    {
        $data = [
            'content' => [],
            'faction' => null,
            'description' => '',
            'name' => 'new deck',
        ];

        $lines = explode("\n", trim($text));

        // trim whitespace off of all lines and filter out any blank lines
        $lines = array_values(
            array_filter(
                array_map(
                    function ($line) {
                        return trim($line);
                    },
                    $lines
                ),
                function ($line) {
                    return '' !== $line;
                }
            )
        );

        if (empty($lines)) {
            throw new Exception('Empty input given.');
        }


        // set the deck's name from the first line in the given import
        $data['name'] = $lines[0];

        foreach ($lines as $line) {
            $matches = [];
            $packNameOrCode = null;
            $card = null;

            if (preg_match('/^\s*(\d)x?([^(]+) \(([^)]+)/u', $line, $matches)) {
                $quantity = intval($matches[1]);
                $name = trim($matches[2]);
                $packNameOrCode = trim($matches[3]);
            } elseif (preg_match('/^\s*(\d)x?([\pLl\pLu\pN\-\.\'\!\:" ]+)/u', $line, $matches)) {
                $quantity = intval($matches[1]);
                $name = trim($matches[2]);
            } elseif (preg_match('/^\s*#\d{3}\s(\d)x?([\pLl\pLu\pN\-\.\'\!\: ]+)/u', $line, $matches)) {
                $quantity = intval($matches[1]);
                $name = trim($matches[2]);
            } elseif (preg_match('/^([^\(]+).*x(\d)/', $line, $matches)) {
                $quantity = intval($matches[2]);
                $name = trim($matches[1]);
            } elseif (preg_match('/^([^\(]+)/', $line, $matches)) {
                $quantity = 1;
                $name = trim($matches[1]);
            } else {
                continue;
            }

            if ($packNameOrCode) {
                /* @var PackInterface $pack */
                $pack = null;
                if (array_key_exists($packNameOrCode, $packsByName)) {
                    $pack = $packsByName[$packNameOrCode];
                } elseif (array_key_exists($packNameOrCode, $packsByCode)) {
                    $pack = $packsByCode[$packNameOrCode];
                }
                if ($pack) {
                    $card = $this->em->getRepository(Card::class)->findOneBy(array(
                        'name' => $name,
                        'pack' => $pack->getId(),
                    ));
                }
            } else {
                $card = $this->em->getRepository(Card::class)->findOneBy(array(
                    'name' => $name
                ));
            }

            if ($card) {
                $data['content'][$card->getCode()] = $quantity;
            } else {
                $faction = $this->em->getRepository(Faction::class)->findOneBy(array(
                    'name' => $name
                ));
                if ($faction) {
                    $data['faction'] = $faction;
                }
            }
        }

        if (empty($data['faction'])) {
            throw new Exception('Unable to find the Faction of the deck.');
        }

        return $data;
    }
}
