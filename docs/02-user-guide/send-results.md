# Send results

- `SendResult::STATUS_SENT` / `STATUS_SKIPPED` / `STATUS_FAILED`
- `SendReport::hasFailures()`, `failures()`, `forChannel()`, `allSuccessful()`

Skipped channels are **not** failures. `allSuccessful()` is true when there is at least one result and none failed.
