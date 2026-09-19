# Release checklist

1. Confirm version with maintainer (this line: `1.0.0`)
2. Branch `release/<version>` from `develop`
3. Update README badges, CHANGELOG, docs version mentions
4. PR into `master`; wait for fully green required checks
5. Tag `v<version>` on `master`
6. Merge `master` back into `develop` via PR
