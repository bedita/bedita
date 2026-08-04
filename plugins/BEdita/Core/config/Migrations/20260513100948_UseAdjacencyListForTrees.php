<?php
declare(strict_types=1);

use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Table;
use Migrations\BaseMigration;

class UseAdjacencyListForTrees extends BaseMigration
{
    /**
     * {@inheritDoc}
     */
    public function up(): void
    {
        $this->table('trees')
            // Rename column `tree_left` in `priority`, and remove column `tree_right`.
            ->renameColumn('tree_left', 'priority')
            ->removeColumn('tree_right')

            // Add index on parent/priority.
            ->addIndex(['parent_id', 'priority'], ['name' => 'trees_parentpriority_idx'])

            // Remove legacy indices.
            ->removeIndexByName('trees_left_idx')
            ->removeIndexByName('trees_right_idx')
            ->removeIndexByName('trees_nsm_idx')

            ->update();

        // Update `trees` table using relative priorities.
        $selectQuery = $this->getQueryBuilder('select');
        $rows = $selectQuery
            ->select([
                'id',
                'priority' => $selectQuery->func()->rowNumber()
                    ->partition('parent_id')
                    ->orderBy(['trees.priority', 'id']),
            ])
            ->from('trees')
            ->orderBy(['id' => 'ASC'])
            ->execute()
            ->fetchAll('assoc');

        foreach (array_chunk($rows, 500) as $chunk) {
            $updateQuery = $this->getQueryBuilder('update');
            $case = $updateQuery->newExpr()->case();
            $ids = [];
            foreach ($chunk as $row) {
                $id = (int)$row['id'];
                $case->when(['id' => $id])->then((int)$row['priority']);
                $ids[] = $id;
            }

            $updateQuery->update('trees')
                ->set('trees.priority', $case)
                ->where(fn (QueryExpression $exp): QueryExpression => $exp->in('id', $ids))
                ->execute();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down(): void
    {
        $this->table('trees')
            // Rename column `priority` in `tree_left`, and restore column `tree_right`.
            ->renameColumn('priority', 'tree_left')
            ->addColumn('tree_right', 'integer', [
                'comment' => 'right counter (for nested set model)',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])

            // Remove index on parent/priority.
            ->removeIndexByName('trees_parentpriority_idx')

            // Restore legacy indices.
            ->addIndex(['tree_left'], ['name' => 'trees_left_idx'])
            ->addIndex(['tree_right'], ['name' => 'trees_right_idx'])
            ->addIndex(
                ['tree_left', 'tree_right'],
                ['name' => 'trees_nsm_idx', 'order' => ['tree_left' => 'ASC', 'tree_right' => 'DESC']],
            )

            ->update();

        // Fix NSM indices by triggering a tree recovery.
        $table = new Table(['table' => 'trees', 'connection' => $this->getAdapter()->getConnection()]);
        $table->addBehavior('BEdita/Core.Tree', ['left' => 'tree_left', 'right' => 'tree_right']);
        $table->nonAtomicRecover();
    }
}
