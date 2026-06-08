<?php 

declare(strict_types=1);

namespace App\Models;

use Core\Model;

final class Certification extends Model
{
      public function getTableData(): array
      {
            $sql = "SELECT c.id, c.code, c.institution, c.start_date, c.end_date, c.is_active, c.created_at, ce.shortname AS certify_name
                  FROM certifications c
                  JOIN certifies ce ON c.certify_id = ce.id
                  ORDER BY c.created_at DESC";

            return $this->get($sql);
      }

      public function findActiveWithCertify(int $certificationId): ?array
      {
            $sql = "SELECT c.id, c.code, c.institution, c.address, c.content, c.certifier, c.signature, c.start_date, c.end_date, ce.name AS certify_name, ce.shortname AS certify_shortname
                  FROM certifications c
                  JOIN certifies ce ON c.certify_id = ce.id
                  WHERE c.id = ? 
                  AND c.is_active IS TRUE
                  AND c.deleted_at IS NULL
                  LIMIT 1";

            return $this->first($sql, [$certificationId]);
      }
}