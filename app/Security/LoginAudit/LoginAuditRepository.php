<?php


namespace Keystone\Security\LoginAudit;

use PDO;
use Keystone\Security\LoginAudit\LoginAuditRepositoryInterface;

final class LoginAuditRepository implements LoginAuditRepositoryInterface {

public function __construct(
        private PDO $db
    ) {}

    public function store(array $data): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO login_audit
             (user_id, ip, country, region, city, timezone, org, created_at)
             VALUES
             (:user_id, :ip, :country, :region, :city, :timezone, :org, NOW())'
        );

        $stmt->bindValue(':user_id', $data['user_id']);
        $stmt->bindValue(':ip', $data['ip']);

        $stmt->bindValue(':country', $data['country'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':region',  $data['region']  ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':city',    $data['city']    ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':timezone',$data['timezone']?? null, PDO::PARAM_STR);
        $stmt->bindValue(':org',     $data['org']     ?? null, PDO::PARAM_STR);

        $stmt->execute();
    }
}


?>