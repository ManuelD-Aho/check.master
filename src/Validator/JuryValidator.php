<?php

declare(strict_types=1);

namespace App\Validator;

class JuryValidator extends AbstractValidator
{
    private const REQUIRED_MEMBER_COUNT = 5;
    private const REQUIRED_PRESIDENT_COUNT = 1;

    public function validateComposition(array $data): bool
    {
        $this->reset();

        $this->validateRequired('id_soutenance', $data['id_soutenance'] ?? null, 'soutenance');
        if (isset($data['id_soutenance']) && $data['id_soutenance'] !== '') {
            $this->validateInteger('id_soutenance', $data['id_soutenance'], 1, null, 'soutenance');
        }

        $this->validateRequired('membres', $data['membres'] ?? null, 'membres du jury');

        if (!isset($data['membres']) || !is_array($data['membres'])) {
            return $this->isValid();
        }

        $membres = $data['membres'];

        if (count($membres) !== self::REQUIRED_MEMBER_COUNT) {
            $this->addError(
                'membres',
                'Le jury doit être composé de exactement ' . self::REQUIRED_MEMBER_COUNT . ' membres.'
            );
        }

        $presidentCount = 0;
        $enseignantIds = [];

        foreach ($membres as $index => $membre) {
            $prefix = "membres[{$index}]";

            if (!isset($membre['id_enseignant']) || $membre['id_enseignant'] === '') {
                $this->addError($prefix . '.id_enseignant', "L'enseignant est obligatoire pour le membre #{$index}.");
            } else {
                $this->validateInteger($prefix . '.id_enseignant', $membre['id_enseignant'], 1, null, "enseignant du membre #{$index}");

                if (in_array($membre['id_enseignant'], $enseignantIds, true)) {
                    $this->addError($prefix . '.id_enseignant', "Un enseignant ne peut pas apparaître plusieurs fois dans le jury.");
                }
                $enseignantIds[] = $membre['id_enseignant'];
            }

            if (!isset($membre['id_role_jury']) || $membre['id_role_jury'] === '') {
                $this->addError($prefix . '.id_role_jury', "Le rôle est obligatoire pour le membre #{$index}.");
            } else {
                $this->validateInteger($prefix . '.id_role_jury', $membre['id_role_jury'], 1, null, "rôle du membre #{$index}");

                if (isset($membre['id_role_jury']) && (string) $membre['id_role_jury'] === '1') {
                    $presidentCount++;
                }
            }
        }

        if ($presidentCount !== self::REQUIRED_PRESIDENT_COUNT) {
            $this->addError('membres', 'Le jury doit avoir exactement ' . self::REQUIRED_PRESIDENT_COUNT . ' président.');
        }

        return $this->isValid();
    }
}
