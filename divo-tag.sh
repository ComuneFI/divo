echo "**Divo-Tag**"
VERSION=$1
echo '[DT] going to set version chip to... ' $VERSION
sed -i 's/        divo_version:.*/        divo_version: "'$VERSION'"/' config/packages/twig.yaml
sed -i 's/softwareVersion: .*/softwareVersion: '$VERSION'/' publiccode.yml
echo '[DT] Completed.'
echo '[DT] You can proceed to commit, tag and push your new version '$VERSION' .'
