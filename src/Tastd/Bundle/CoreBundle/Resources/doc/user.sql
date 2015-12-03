

Select friends.id, friends.full_name, friends.common_friends_count
FROM
    (SELECT xxx FROM users) as friends

ORDER BY common_friends_count



## GET MY LEADERS

(SELECT connections.leader_id FROM connections
WHERE connections.follower_id = 1
AND connections.status = 'APPROVED') AS my_leaders


## GET FOLLOWER WHO HAVE THOSE LEADERS
SELECT connections.follower_id FROM connections
WHERE connections.leader_id IN (2,3,4)
AND connections.status = 'APPROVED' AS my_leaders


## GET USERS WHO HAVE MY SAME LEADERS
SELECT connections.follower_id, COUNT(DISTINCT(connections.leader_id)) FROM connections
WHERE connections.leader_id IN
    (SELECT connections.leader_id FROM connections
    WHERE connections.follower_id = 1
    AND connections.status = 'APPROVED')
AND connections.follower_id <> 1
AND connections.status = 'APPROVED'


## GET COMMON FOLLOWER COUNT
        SELECT
            results.user_id as id,
            results.full_name as full_name,
            SUM(results.common_followers_count) as common_followers_count
        FROM (
            SELECT
                users.id as user_id,
                users.full_name as full_name,
                0 as common_followers_count
                FROM users
                WHERE users.id <> 1

            UNION ALL
            SELECT
                c2.leader_id as user_id,
                '' as full_name,
                COUNT(c1.follower_id) as common_followers_count
            FROM connections c1
            INNER JOIN connections c2
            ON c1.follower_id = c2.follower_id
            AND c1.leader_id <> c2.leader_id
            WHERE c1.leader_id = 1
            GROUP BY c1.leader_id, c2.leader_id
        ) AS results

        GROUP BY id
        HAVING full_name LIKE "%r%"
        ORDER BY common_followers_count DESC
        LIMIT 0, 10


## GET COMMON LEADER COUNT
SELECT c1.follower_id AS user1
, c2.follower_id as user2
, COUNT(c1.leader_id) as common_leaders_count
FROM connections c1
INNER JOIN connections c2
ON c1.leader_id = c2.leader_id AND c1.follower_id <> c2.follower_id
WHERE c1.follower_id = 1
GROUP BY c1.follower_id, c2.follower_id









